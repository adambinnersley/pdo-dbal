<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\XcacheCache;

class XcacheCacheTest extends CacheTest{
    
    public function setUp() {
        if(!extension_loaded('xcache')) {
            $this->markTestSkipped(
                'The XCache extension is not available.'
            );
        }
    }
    
}
