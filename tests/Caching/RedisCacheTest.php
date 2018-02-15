<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\RedisCache;

class RedisCacheTest extends CacheTest{
    
    protected $port = 6379;
    
    /**
     * @covers \DBAL\Caching\RedisCache::__construct
     * @covers \DBAL\Caching\RedisCache::connect
     * @covers \DBAL\Caching\RedisCache::addServer
     * @covers \DBAL\Caching\RedisCache::save
     * @covers \DBAL\Caching\RedisCache::fetch
     */
    public function setUp() {
        $success = false;
        $this->cache = new RedisCache();
        $this->cache->connect('127.0.0.1', $this->port);
        if ($this->cache->save('test', 'Success')) {
            if ($this->cache->fetch('test') == 'Success') {
                $success = true;
                parent::setUp();
            }
        }
        if($success !== true){
            $this->markTestSkipped('Redis extension may not be loaded');
        }
    }
    
}
