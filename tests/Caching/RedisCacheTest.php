<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\RedisCache;

class RedisCacheTest extends CacheTest{
    
    protected $port = 6379;
    
    public function setUp() {
        if(!extension_loaded('redis')) {
            $this->markTestSkipped(
                'The Redis extension is not available.'
            );
        }
    }
    
}
