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
        $this->cache = new RedisCache();
        parent::setUp();
    }
    
    public function testCacheClear(){
        $this->cache->save('key1', 'testvalue', 60);
        $this->assertEquals(1, $this->cache->deleteAll());
    }
    
}
