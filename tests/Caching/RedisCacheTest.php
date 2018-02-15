<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\RedisCache;

class RedisCacheTest extends CacheTest{
    
    protected $port = 6379;
    
    /**
     * @covers DBAL\Caching\RedisCache
     */
    public function setUp() {
        try{
        $this->cache = new RedisCache();
        $this->cache->connect('127.0.0.1', $this->port);
        }
        catch(\RedisException $e){
            $this->markTestSkipped(
                'The Redis extension is not available.'
            );
        }
    }
    
}
