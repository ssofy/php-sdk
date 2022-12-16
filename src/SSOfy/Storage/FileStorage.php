<?php

namespace SSOfy\Storage;

use SSOfy\Helper;

class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $storagePath;

    public function __construct($storagePath)
    {
        $this->storagePath = $storagePath;
    }

    public function put($key, $value, $ttl = 0)
    {
        $this->delete($key);

        file_put_contents($this->getFilename($key) . ".$ttl", $value);
    }

    public function get($key)
    {
        $files = glob($this->getFilename($key) . '.*');
        if (empty($files)) {
            return null;
        }

        $ttl = intval(substr($files[0], strpos($files[0], '.') + 1));

        if ($ttl > 0 && time() >= filemtime($files[0]) + $ttl) {
            $this->delete($key);
            return null;
        }

        return file_get_contents($files[0]);
    }

    public function delete($key)
    {
        array_map('unlink', glob($this->getFilename($key) . '.*'));
    }

    public function flushAll()
    {
        array_map('unlink', glob(Helper::pathJoin($this->storagePath, '*.*')));
    }

    public function cleanup()
    {
        array_map(function ($filename) {
            if (substr($filename, 0, 1) === '.') {
                return;
            }

            $ttl = intval(substr($filename, strpos($filename, '.') + 1));

            $filename = Helper::pathJoin($this->storagePath, $filename);
            if ($ttl > 0 && time() >= filemtime($filename) + $ttl) {
                unlink($filename);
            }
        }, scandir($this->storagePath));
    }

    private function getFilename($key)
    {
        return Helper::pathJoin($this->storagePath, str_replace('/', ':', $key));
    }
}
