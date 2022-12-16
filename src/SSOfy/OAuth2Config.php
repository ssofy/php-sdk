<?php

namespace SSOfy;

use League\OAuth2\Client\Provider\GenericProvider;
use SSOfy\Storage\StorageInterface;

class OAuth2Config
{
    /**
     * @var array
     */
    private $config;

    public function __construct($config = [])
    {
        $default = [
            'client_id'          => null,
            'client_secret'      => null,
            'authorize_url'      => null,
            'token_url'          => null,
            'resource_owner_url' => null,
            'redirect_uri'       => null,
            'pkce_verification'  => true,
            'pkce_method'        => 'S256',
            'timeout'            => 60 * 60, // 1 hour
            'scopes'             => [],
            'otp'                => null,
            'session_store'      => null,
            'state_store'        => null,
            'state_ttl'          => 60 * 60 * 24 * 365, // 1 year
        ];

        $this->config = array_merge($default, $config);
        $this->config = array_intersect_key($this->config, $default);

        foreach ($this->config as $key => $val) {
            $fn = Helper::snakeToCamel($key);
            $this->$fn($val);
        }
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function clientId($value = null)
    {
        return $this->getOrSet('client_id', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function clientSecret($value = null)
    {
        return $this->getOrSet('client_secret', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function authorizeUrl($value = null)
    {
        return $this->getOrSet('authorize_url', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function tokenUrl($value = null)
    {
        return $this->getOrSet('token_url', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function resourceOwnerUrl($value = null)
    {
        return $this->getOrSet('resource_owner_url', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function redirectUri($value = null)
    {
        return $this->getOrSet('redirect_uri', $value);
    }

    /**
     * @param boolean $value
     * @return $this|boolean
     */
    public function pkceVerification($value = null)
    {
        return $this->getOrSet('pkce_verification', boolval($value));
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function pkceMethod($value = null)
    {
        if (is_string($value)) {
            $value = strtolower($value);

            switch ($value) {
                case 's256':
                    $value = GenericProvider::PKCE_METHOD_S256;
                    break;

                case 'plain':
                    $value = GenericProvider::PKCE_METHOD_PLAIN;
                    break;
            }
        }

        return $this->getOrSet('pkce_method', $value);
    }

    /**
     * @param int $value
     * @return $this|int
     */
    public function timeout($value = null)
    {
        return $this->getOrSet('timeout', $value);
    }

    /**
     * @param array $value
     * @return $this|array
     */
    public function scopes($value = null)
    {
        return $this->getOrSet('scopes', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function otp($value = null)
    {
        return $this->getOrSet('otp', $value);
    }

    /**
     * @param StorageInterface $value
     * @return $this|StorageInterface
     */
    public function sessionStore($value = null)
    {
        return $this->getOrSet('session_store', $value);
    }

    /**
     * @param StorageInterface $value
     * @return $this|StorageInterface
     */
    public function stateStore($value = null)
    {
        return $this->getOrSet('state_store', $value);
    }

    /**
     * @param int $value
     * @return $this|int
     */
    public function stateTtl($value = null)
    {
        return $this->getOrSet('state_ttl', $value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

    private function getOrSet($key, $val)
    {
        if (is_null($val)) {
            return $this->config[$key];
        }

        $this->config[$key] = $val;

        return $this;
    }
}
