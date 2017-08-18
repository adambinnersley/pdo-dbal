<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\MemcacheCache;

class MemcacheCacheTest extends CacheTest{
    
    protected $port = 11211;
    
    public function setUp() {
        if(!extension_loaded('memcache')) {
            $this->markTestSkipped(
                'The memcache extension is not available.'
            );
        }
        $this->cache = new MemcacheCache();
    }
    
}
