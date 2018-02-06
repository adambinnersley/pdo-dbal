<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\MemcachedCache;

class MemcachedCacheTest extends CacheTest{
    
    protected $port = 11211;
    
    /**
     * @covers DBAL\Caching\MemcachedCache
     */
    public function setUp() {
        if(!extension_loaded('memcached')) {
            $this->markTestSkipped(
                'The memcached extension is not available.'
            );
        }
        $this->cache = new MemcachedCache();
        parent::setUp();
    }
}
