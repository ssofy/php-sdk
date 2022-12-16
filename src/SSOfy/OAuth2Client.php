<?php

namespace SSOfy;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;
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
     * @var StorageInterface
     */
    private $sessionStore;

    /**
     * @param OAuth2Config $config
     */
    public function __construct($config)
    {
        $this->config       = $config;
        $this->stateStore   = empty($config->stateStore()) ? new NullStorage() : $config->stateStore();
        $this->sessionStore = empty($config->sessionStore()) ? new NullStorage() : $config->sessionStore();
    }

    /**
     * @return string
     */
    public function getSessionState()
    {
        return $this->sessionStore->get($this->stateSessionKey());
    }

    /**
     * @param string $uri the requested uri
     * @return string
     */
    public function initAuthCodeFlow($uri)
    {
        $oauthConfig = $this->buildLeagueConfig($this->config);

        $stateData = [
            'uri'    => $uri,
            'config' => $this->config->toArray(),
        ];

        $provider = new GenericProvider($oauthConfig);

        $authorizationUrl = $provider->getAuthorizationUrl();
        $state            = $provider->getState();

        if ($this->config->pkceVerification()) {
            $stateData['pkce_code'] = $provider->getPkceCode();
        }

        $this->saveState($state, $stateData, $this->config->timeout());

        return $authorizationUrl;
    }

    /**
     * @param string $state
     * @param string $code
     * @return void
     */
    public function continueAuthCodeFlow($state, $code)
    {
        $stateData = $this->getState($state);

        $config = new OAuth2Config($stateData['config']);

        $provider = new GenericProvider($this->buildLeagueConfig($config));

        if ($config->pkceVerification()) {
            $provider->setPkceCode($stateData['pkce_code']);
        }

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        $stateData['access_token'] = $accessToken;

        $this->saveState($state, $stateData, $this->config->stateTtl());

        $this->sessionStore->put($this->stateSessionKey(), $state);

        return $stateData['uri'];
    }

    /**
     * @param string $state
     * @return OAuth2Config|null
     */
    public function getConfig($state)
    {
        $stateData = $this->getState($state);

        if (!isset($stateData['config'])) {
            return null;
        }

        return new OAuth2Config($stateData['config']);
    }

    /**
     * @param string $state
     * @return mixed|null
     */
    public function getUserInfo($state)
    {
        $stateData = $this->getState($state);

        if (!isset($stateData['user'])) {
            return $this->refreshUserInfo($state);
        }

        return $stateData['user'];
    }

    /**
     * @param string $state
     * @return ResourceOwnerInterface|null
     */
    public function refreshUserInfo($state)
    {
        $stateData = $this->getState($state);

        if (!isset($stateData['access_token'])) {
            return null;
        }

        $provider = new GenericProvider($stateData['config']);

        $user = $provider->getResourceOwner($stateData['access_token']);

        $stateData['user'] = $user;

        $this->saveState($state, $stateData, $this->config->stateTtl());

        return $user;
    }

    /**
     * @param string $state
     * @return AccessTokenInterface
     */
    public function getAccessToken($state)
    {
        $stateData = $this->getState($state);

        if (!isset($stateData['access_token'])) {
            return null;
        }

        /** @var AccessTokenInterface $accessToken */
        $accessToken = $stateData['access_token'];

        if (!$accessToken->hasExpired()) {
            return $accessToken;
        }

        $config = new OAuth2Config($stateData['config']);

        $provider = new GenericProvider($this->buildLeagueConfig($config));

        if ($config->pkceVerification()) {
            $provider->setPkceCode($stateData['pkce_code']);
        }

        $accessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $accessToken->getRefreshToken()
        ]);

        $stateData['access_token'] = $accessToken;

        $this->saveState($state, $stateData, $this->config->stateTtl());

        return $accessToken;
    }

    /**
     * @param string $state
     * @return void
     */
    public function deleteState($state)
    {
        $this->stateStore->delete($this->stateStorageKey($state));
        $this->sessionStore->delete($this->stateSessionKey());
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

        unset($data['config']['session_store']);
        unset($data['config']['state_store']);

        $this->stateStore->put($key, serialize($data), $timeout);
    }

    private function stateStorageKey($state)
    {
        return "oauth:state:$state";
    }

    private function stateSessionKey()
    {
        return "oauth:workflow-state";
    }

    /**
     * @param OAuth2Config $config
     * @return array
     */
    private function buildLeagueConfig($config)
    {
        $authorizeUrl = $config->authorizeUrl();

        if (!is_null($config->otp())) {
            $authorizeUrl = Helper::addUrlParams($authorizeUrl, [
                'token' => $config->otp()
            ]);
        }

        return [
            'clientId'                => $config->clientId(),
            'clientSecret'            => $config->clientSecret(),
            'redirectUri'             => $config->redirectUri(),
            'urlAuthorize'            => $authorizeUrl,
            'urlAccessToken'          => $config->tokenUrl(),
            'urlResourceOwnerDetails' => $config->resourceOwnerUrl(),
            'pkceMethod'              => $config->pkceVerification() ? $config->pkceMethod() : null,
            'scopes'                  => implode(' ', $config->scopes()),
            'scopeSeparator'          => ' ',
        ];
    }
}
