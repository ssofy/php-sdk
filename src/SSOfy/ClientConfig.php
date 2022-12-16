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
            $fn = Helper::snakeToCamel($key);
            $this->$fn($val);
        }
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function domain($value = null)
    {
        return $this->getOrSet('domain', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function key($value = null)
    {
        return $this->getOrSet('key', $value);
    }

    /**
     * @param string $value
     * @return $this|string
     */
    public function secret($value = null)
    {
        return $this->getOrSet('secret', $value);
    }

    /**
     * @param StorageInterface $value
     * @return $this|StorageInterface
     */
    public function cacheStore($value = null)
    {
        return $this->getOrSet('cache_store', $value);
    }

    /**
     * @param int $value
     * @return $this|int
     */
    public function cacheTtl($value = null)
    {
        return $this->getOrSet('cache_ttl', $value);
    }

    /**
     * @param boolean $value
     * @return $this|boolean
     */
    public function secure($value = null)
    {
        return $this->getOrSet('secure', $value);
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
