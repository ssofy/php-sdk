<?php

namespace SSOfy\Storage;

class SessionStorage implements StorageInterface
{
    public function put($key, $value, $ttl = 0)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key];
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function flushAll()
    {
        session_unset();
    }

    public function cleanup()
    {
        // do nothing
    }
}
