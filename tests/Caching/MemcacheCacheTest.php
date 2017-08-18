<?php
namespace DBAL\Tests\Caching;

use DBAL\Database;
use DBAL\Caching\MemcacheCache;
use PHPUnit\Framework\TestCase;

class MemcacheCacheTest extends TestCase{
    
    public function setUp() {
        if(!extension_loaded('memcache')) {
            $this->markTestSkipped(
                'The memcache extension is not available.'
            );
        }
    }
    
}
