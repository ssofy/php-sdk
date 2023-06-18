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
            'url'               => null,
            'client_id'         => null,
            'client_secret'     => null,
            'redirect_uri'      => null,
            'pkce_verification' => true,
            'pkce_method'       => 'S256',
            'timeout'           => 60 * 60, // 1 hour
            'scopes'            => [],
            'locale'            => null,
            'state_ttl'         => 60 * 60 * 24 * 365, // 1 year
            'state_store'       => null,
        ];

        $this->config = array_merge($default, $config);
        $this->config = array_intersect_key($this->config, $default);

        foreach ($this->config as $key => $val) {
            $fn = 'set' . Helper::snakeToCamel($key);
            $this->$fn($val);
        }
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setUrl($value)
    {
        $this->config['url'] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->config['client_id'];
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setClientId($value)
    {
        $this->config['client_id'] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->config['client_secret'];
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setClientSecret($value)
    {
        $this->config['client_secret'] = $value;
        return $this;
    }

    /**
     * @param string|null $token
     * @return string|null
     */
    public function getAuthorizeUrl($token = null)
    {
        if (empty($this->config['url'])) {
            return null;
        }

        $params = [];

        if (!is_null($token)) {
            $params['token'] = $token;
        }

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], "/authorize"), $params);
    }

    /**
     * @param string $provider
     * @return string|null
     */
    public function getSocialAuthorizeUrl($provider)
    {
        if (empty($this->config['url'])) {
            return null;
        }

        $provider = strtolower($provider);

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], "/social/{$provider}/authorize"));
    }

    /**
     * @return string|null
     */
    public function getTokenUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return Helper::urlJoin($this->config['url'], '/token');
    }

    /**
     * @return string|null
     */
    public function getLogoutUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], '/logout'));
    }

    /**
     * @return string|null
     */
    public function getLogoutEverywhereUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], '/logout-everywhere'));
    }

    /**
     * @return string|null
     */
    public function getRegisterUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], '/register'));
    }

    /**
     * @return string|null
     */
    public function getAccountUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return $this->addUrlParams(Helper::urlJoin($this->config['url'], '/account'));
    }

    /**
     * @return string|null
     */
    public function getResourceOwnerUrl()
    {
        if (empty($this->config['url'])) {
            return null;
        }

        return Helper::urlJoin($this->config['url'], '/userinfo');
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
     * @return string|null
     */
    public function getLocale()
    {
        return $this->config['locale'];
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setLocale($value)
    {
        $this->config['locale'] = $value;
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
        return array_merge($this->config, [
            'authorize_url'         => $this->getAuthorizeUrl(),
            'token_url'             => $this->getTokenUrl(),
            'logout_url'            => $this->getLogoutUrl(),
            'logout_everywhere_url' => $this->getLogoutEverywhereUrl(),
            'resource_owner_url'    => $this->getResourceOwnerUrl(),
            'account_url'           => $this->getAccountUrl(),
        ]);
    }

    private function addUrlParams($url, $extraParams = [])
    {
        $params = $extraParams;

        $locale      = $this->getLocale();
        $redirectUrl = $this->getRedirectUri();

        if (!empty($locale)) {
            $params['locale'] = $locale;
        }

        if (!empty($redirectUrl)) {
            $params['redirect_uri'] = $redirectUrl;
        }

        if (!empty($params)) {
            $url = Helper::addUrlParams($url, $params);
        }

        return $url;
    }
}
