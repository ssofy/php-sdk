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
            'token'              => null,
            'session_store'      => null,
            'state_store'        => null,
            'state_ttl'          => 60 * 60 * 24 * 365, // 1 year
        ];

        $this->config = array_merge($default, $config);
        $this->config = array_intersect_key($this->config, $default);

        foreach ($this->config as $key => $val) {
            $fn = 'set' . Helper::snakeToCamel($key);
            $this->$fn($val);
        }
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->config['client_id'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setClientId($value)
    {
        $this->config['client_id'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->config['client_secret'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setClientSecret($value)
    {
        $this->config['client_secret'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizeUrl()
    {
        return $this->config['authorize_url'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAuthorizeUrl($value)
    {
        $this->config['authorize_url'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getTokenUrl()
    {
        return $this->config['token_url'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTokenUrl($value)
    {
        $this->config['token_url'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceOwnerUrl()
    {
        return $this->config['resource_owner_url'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setResourceOwnerUrl($value)
    {
        $this->config['resource_owner_url'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->config['redirect_uri'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setRedirectUri($value)
    {
        $this->config['redirect_uri'] = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPkceVerification()
    {
        return $this->config['pkce_verification'];
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setPkceVerification($value)
    {
        $this->config['pkce_verification'] = boolval($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPkceMethod()
    {
        return $this->config['pkce_method'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPkceMethod($value)
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

        $this->config['pkce_method'] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->config['timeout'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setTimeout($value)
    {
        $this->config['timeout'] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->config['scopes'];
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setScopes($value)
    {
        $this->config['scopes'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->config['token'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setToken($value)
    {
        $this->config['token'] = $value;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getSessionStore()
    {
        return $this->config['session_store'];
    }

    /**
     * @param StorageInterface $value
     * @return $this
     */
    public function setSessionStore($value)
    {
        $this->config['session_store'] = $value;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStateStore()
    {
        return $this->config['state_store'];
    }

    /**
     * @param StorageInterface $value
     * @return $this
     */
    public function setStateStore($value)
    {
        $this->config['state_store'] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getStateTtl()
    {
        return $this->config['state_ttl'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStateTtl($value)
    {
        $this->config['state_ttl'] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }
}
