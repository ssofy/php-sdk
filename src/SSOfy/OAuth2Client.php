<?php

namespace SSOfy;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;
use SSOfy\Exceptions\AuthErrorException;
use SSOfy\Exceptions\InvalidStateException;
use SSOfy\Storage\NullStorage;
use SSOfy\Storage\StorageInterface;

class OAuth2Client
{
    /**
     * @var OAuth2Config
     */
    private $config;

    /**
     * @var StorageInterface
     */
    private $stateStore;

    /**
     * @param OAuth2Config $config
     */
    public function __construct($config)
    {
        $this->config     = $config;
        $this->stateStore = empty($config->getStateStore()) ? new NullStorage() : $config->getStateStore();
    }

    /**
     * @param string|null $authorizationUrl custom authorization url. Defaults to AuthConfig::getAuthorizationUrl().
     * @param string|null $nextUri the uri to be continued after successful authorization
     * @return array
     */
    public function initAuthCodeFlow($authorizationUrl = null, $nextUri = null)
    {
        while (!isset($state) || !is_null($this->stateStore->get($this->stateStorageKey($state)))) {
            $provider = new GenericProvider($this->buildLeagueConfig($this->config, $authorizationUrl));
            $authUrl  = $provider->getAuthorizationUrl();
            $state    = $provider->getState();
        }

        $stateData = [
            'uri'    => $nextUri,
            'config' => $this->config->toArray(),
        ];

        if ($this->config->getPkceVerification()) {
            $stateData['code_verifier'] = $provider->getPkceCode();
        }

        $this->saveState($state, $stateData, $this->config->getTimeout());

        return array_merge($stateData, [
            'state' => $state,
            'uri'   => $authUrl,
        ]);
    }

    /**
     * @param array $payload
     * @return array
     * @throws InvalidStateException
     * @throws AuthErrorException
     */
    public function handleCallback($payload)
    {
        $state = $payload['state'];

        $stateData = $this->getState($state);

        if (is_null($stateData)) {
            throw new InvalidStateException();
        }

        $config = new OAuth2Config($stateData['config']);

        $provider = new GenericProvider($this->buildLeagueConfig($config));

        if ($config->getPkceVerification() && isset($stateData['code_verifier'])) {
            $provider->setPkceCode($stateData['code_verifier']);
        }

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $state,
            ]);
        } catch (\Exception $exception) {
            throw new AuthErrorException($exception->getMessage());
        }

        $stateData['token'] = $accessToken;

        $this->saveState($state, $stateData, $this->config->getStateTtl());

        return $stateData;
    }

    /**
     * @param string $state
     * @return OAuth2Config|null
     * @throws InvalidStateException
     */
    public function getConfig($state)
    {
        $stateData = $this->getState($state);

        if (is_null($stateData)) {
            throw new InvalidStateException();
        }

        if (!isset($stateData['config'])) {
            return null;
        }

        return new OAuth2Config($stateData['config']);
    }

    /**
     * @param string $state
     * @return mixed|null
     * @throws InvalidStateException
     */
    public function getUserInfo($state)
    {
        $stateData = $this->getState($state);

        if (is_null($stateData)) {
            throw new InvalidStateException();
        }

        if (!isset($stateData['user'])) {
            return $this->refreshUserInfo($state);
        }

        return $stateData['user'];
    }

    /**
     * @param string $state
     * @return ResourceOwnerInterface|null
     * @throws InvalidStateException
     */
    public function refreshUserInfo($state)
    {
        $stateData = $this->getState($state);

        if (is_null($stateData)) {
            throw new InvalidStateException();
        }

        if (!isset($stateData['token'])) {
            throw new InvalidStateException();
        }

        $provider = new GenericProvider($stateData['config']);

        $user = $provider->getResourceOwner($stateData['token']);

        $stateData['user'] = $user;

        $this->saveState($state, $stateData, $this->config->getStateTtl());

        return $user;
    }

    /**
     * @param string $state
     * @return AccessTokenInterface
     * @throws InvalidStateException
     */
    public function getAccessToken($state)
    {
        $stateData = $this->getState($state);

        if (is_null($stateData)) {
            throw new InvalidStateException();
        }

        if (!isset($stateData['token'])) {
            throw new InvalidStateException();
        }

        /** @var AccessTokenInterface $accessToken */
        $accessToken = $stateData['token'];

        if (!$accessToken->hasExpired()) {
            return $accessToken;
        }

        $config = new OAuth2Config($stateData['config']);

        $provider = new GenericProvider($this->buildLeagueConfig($config));

        if ($config->getPkceVerification()) {
            $provider->setPkceCode($stateData['code_verifier']);
        }

        try {
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken()
            ]);
        } catch (IdentityProviderException $exception) {
            return null;
        }

        $stateData['token'] = $accessToken;

        $this->saveState($state, $stateData, $this->config->getStateTtl());

        return $accessToken;
    }

    /**
     * @param OAuth2Config $config
     * @param string|null $authorizeUrl
     * @return array
     */
    private function buildLeagueConfig($config, $authorizeUrl = null)
    {
        if (is_null($authorizeUrl)) {
            $authorizeUrl = $config->getAuthorizationUrl();
        }

        return [
            'clientId'                => $this->config->getClientId(),
            'clientSecret'            => $this->config->getClientSecret(),
            'redirectUri'             => $this->config->getRedirectUri(),
            'urlAuthorize'            => $authorizeUrl,
            'urlAccessToken'          => $this->config->getTokenUrl(),
            'urlResourceOwnerDetails' => $this->config->getResourceOwnerUrl(),
            'pkceMethod'              => $this->config->getPkceVerification() ? $this->config->getPkceMethod() : null,
            'scopes'                  => implode(' ', $this->config->getScopes()),
            'scopeSeparator'          => ' ',
        ];
    }

    /**
     * @param string $state
     * @return array|null
     */
    private function getState($state)
    {
        return unserialize($this->stateStore->get($this->stateStorageKey($state)));
    }

    /**
     * @param string $state
     * @param array $data
     * @param int $timeout seconds
     * @return void
     */
    private function saveState($state, $data, $timeout = 0)
    {
        $key = $this->stateStorageKey($state);

        $this->stateStore->delete($key);

        $this->stateStore->put($key, serialize($data), $timeout);
    }

    /**
     * @param string $state
     * @return void
     */
    public function deleteState($state)
    {
        $this->stateStore->delete($this->stateStorageKey($state));
    }

    private function stateStorageKey($state)
    {
        return "oauth:state:$state";
    }
}
