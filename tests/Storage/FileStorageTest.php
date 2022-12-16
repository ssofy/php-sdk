<?php

use SSOfy\Storage\FileStorage;
use SSOfy\Helper;

class FileStorageTest extends BaseStorageTest
{
    private $storagePath;

    public function setUp(): void
    {
        $storagePath = '/tmp/ssofy_cache';

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $this->cache       = new FileStorage($storagePath);
        $this->storagePath = $storagePath;
    }

    public function test_write_read()
    {
        parent::test_write_read();
    }

    public function test_write_read_path()
    {
        parent::test_write_read_path();
    }

    public function test_expiration()
    {
        parent::test_expiration();
    }

    public function test_deletion()
    {
        parent::test_deletion();
    }

    public function test_flush_all()
    {
        parent::test_flush_all();
    }

    public function test_cleanup()
    {
        $this->cache->put('test', 'something');
        $this->cache->cleanup();
        $this->assertEquals('something', $this->cache->get('test'));


        $this->cache->put('test', 'something', 2);

        sleep(1);
        $this->cache->cleanup();
        $this->assertFileExists(Helper::pathJoin($this->storagePath, 'test.2'));

        sleep(1);
        $this->cache->cleanup();
        $this->assertFileDoesNotExist(Helper::pathJoin($this->storagePath, 'test.2'));
    }
}
