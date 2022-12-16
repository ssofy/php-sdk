<?php

use PHPUnit\Framework\TestCase;

class BaseStorageTest extends TestCase
{
    protected $cache;

    /**
     * @group skip
     */
    public function test_write_read()
    {
        $this->cache->put('test', 'something');

        $this->assertEquals('something', $this->cache->get('test'));
    }

    /**
     * @group skip
     */
    public function test_write_read_path()
    {
        $this->cache->put('path/test', 'something');
        $this->assertEquals('something', $this->cache->get('path/test'));

        $this->cache->delete('path/test');
        $this->assertEquals(null, $this->cache->get('path/test'));
    }

    /**
     * @group skip
     */
    public function test_expiration()
    {
        $this->cache->put('test', 'something', 2);

        sleep(1);
        $this->assertEquals('something', $this->cache->get('test'));

        sleep(1);
        $this->assertEquals(null, $this->cache->get('test'));
    }

    /**
     * @group skip
     */
    public function test_deletion()
    {
        $this->cache->put('test', 'something');
        $this->assertEquals('something', $this->cache->get('test'));

        $this->cache->delete('test');
        $this->assertEquals(null, $this->cache->get('test'));
    }

    /**
     * @group skip
     */
    public function test_flush_all()
    {
        $this->cache->put('test', 'something');
        $this->assertEquals('something', $this->cache->get('test'));

        $this->cache->flushAll();
        $this->assertEquals(null, $this->cache->get('test'));
    }
}
