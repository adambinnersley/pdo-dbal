<?php
namespace DBAL\Tests\Caching;

use DBAL\Database;
use DBAL\Caching\MemcachedCache;
use PHPUnit\Framework\TestCase;

class MemcachedCacheTest extends TestCase{
    
    public function setUp() {
        if(!extension_loaded('memcached')) {
            $this->markTestSkipped(
                'The memcached extension is not available.'
            );
        }
    }
    
}
