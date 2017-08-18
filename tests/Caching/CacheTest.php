<?php
namespace DBAL\Tests\Caching;

use DBAL\Database;
use PHPUnit\Framework\TestCase;

abstract class CacheTest extends TestCase{
    
    private $cache;
    
    public function setUp() {
        if(!extension_loaded('apc')) {
            $this->markTestSkipped(
                'The APC extension is not available.'
            );
        }
    }
    
    public function tearDown() {
        unset($this->cache);
    }
    
    public function testCacheAdd(){
        
    }
    
    public function testCacheRetrieve(){
        
    }
    
    public function testCacheOverride(){
        
    }
    
    public function testCacheDelete(){
        
    }
    
    public function testCacheClear(){
        
    }
}
