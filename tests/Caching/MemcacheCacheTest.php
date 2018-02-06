<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\MemcacheCache;

class MemcacheCacheTest extends CacheTest{
    
    protected $host = '127.0.0.1';
    protected $port = 11211;
    
    /**
     * @covers DBAL\Caching\MemcacheCache
     */
    public function setUp() {
        if(!extension_loaded('memcache')) {
            $this->markTestSkipped(
                'The memcache extension is not available.'
            );
        }
        $this->cache = new MemcacheCache();
        parent::setUp();
    }
    
}
