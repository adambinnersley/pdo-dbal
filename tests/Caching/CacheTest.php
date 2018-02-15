<?php
namespace DBAL\Tests\Caching;

use PHPUnit\Framework\TestCase;

abstract class CacheTest extends TestCase{
    
    protected $host = '127.0.0.1';
    protected $port = 1;
        
    protected $cache;
    
    public function setUp() {
        $this->cache->connect($this->host, $this->port);
        if(!$this->cache->save('servicetest', 'isactive', 60)){
            $this->markTestSkipped(
                'No connection is available to the caching server'
            );
        }
    }
    
    public function tearDown() {
        unset($this->cache);
    }
    
    public function testConnect(){
        $this->assertObjectHasAttribute('cache', $this->cache->connect($this->host, $this->port));
    }
    
    public function testCacheAdd(){
        $this->assertTrue($this->cache->save('key1', 'testvalue', 60));
    }
    
    public function testCacheRetrieve(){
        $this->assertEquals('testvalue', $this->cache->fetch('key1'));
    }
    
    public function testCacheOverride(){
        $this->cache->replace('key1', 'newvalue', 60);
        $this->assertEquals('newvalue', $this->cache->fetch('key1'));
    }
    
    public function testCacheDelete(){
        $this->assertTrue($this->cache->delete('key1'));
        $this->assertFalse($this->cache->delete('key1'));
    }
    
    public function testCacheClear(){
        $this->cache->save('key1', 'testvalue', 60);
        $this->assertTrue($this->cache->deleteAll());
    }
}
