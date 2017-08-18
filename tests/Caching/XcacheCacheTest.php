<?php
namespace DBAL\Tests\Caching;

use DBAL\Database;
use DBAL\Caching\XcacheCache;
use PHPUnit\Framework\TestCase;

class XcacheCacheTest extends TestCase{
    
    public function setUp() {
        if(!extension_loaded('xcache')) {
            $this->markTestSkipped(
                'The XCache extension is not available.'
            );
        }
    }
    
}
