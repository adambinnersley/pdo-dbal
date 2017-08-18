<?php
namespace DBAL\Tests\Caching;

use DBAL\Database;
use DBAL\Caching\RedisCache;
use PHPUnit\Framework\TestCase;

class RedisCacheTest extends TestCase{
    
    public function setUp() {
        if(!extension_loaded('redis')) {
            $this->markTestSkipped(
                'The Redis extension is not available.'
            );
        }
    }
    
}
