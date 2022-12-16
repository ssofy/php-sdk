<?php

namespace SSOfy\Storage;

interface StorageInterface
{
    /**
     * @param string $key
     * @param string $value
     * @param int $ttl time-to-live in seconds
     * @return void
     */
    public function put($key, $value, $ttl = 0);

    /**
     * @param string $key
     * @return string|null
     */
    public function get($key);

    /**
     * @param string $key
     * @return void
     */
    public function delete($key);

    /**
     * @return void
     */
    public function flushAll();

    /**
     * @return void
     */
    public function cleanup();
}
