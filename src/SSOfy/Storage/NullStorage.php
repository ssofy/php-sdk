<?php

namespace SSOfy\Storage;

class NullStorage implements StorageInterface
{
    public function put($key, $value, $ttl = 0)
    {
        // do nothing
    }

    public function get($key)
    {
        return null;
    }

    public function delete($key)
    {
        // do nothing
    }

    public function flushAll()
    {
        // do nothing
    }

    public function cleanup()
    {
        // do nothing
    }
}
