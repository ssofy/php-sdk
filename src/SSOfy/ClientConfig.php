<?php

namespace SSOfy;

use SSOfy\Storage\StorageInterface;

class ClientConfig
{
    /**
     * @var array
     */
    private $config;

    public function __construct($config = [])
    {
        $default = [
            'domain'      => null,
            'key'         => null,
            'secret'      => null,
            'cache_store' => null,
            'cache_ttl'   => 60 * 60 * 3, // 3 hours
            'secure'      => true,
        ];

        $this->config = array_merge($default, $config);
        $this->config = array_intersect_key($this->config, $default);

        foreach ($this->config as $key => $val) {
            $fn = 'set' . Helper::snakeToCamel($key);
            $this->$fn($val);
        }
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDomain($value)
    {
        $this->config['domain'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->config['domain'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setKey($value)
    {
        $this->config['key'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->config['key'];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSecret($value)
    {
        $this->config['secret'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->config['secret'];
    }

    /**
     * @param StorageInterface $value
     * @return $this
     */
    public function setCacheStore($value)
    {
        $this->config['cache_store'] = $value;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getCacheStore()
    {
        return $this->config['cache_store'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCacheTtl($value)
    {
        $this->config['cache_ttl'] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTtl()
    {
        return $this->config['cache_ttl'];
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setSecure($value)
    {
        $this->config['secure'] = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSecure()
    {
        return $this->config['secure'];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }
}
