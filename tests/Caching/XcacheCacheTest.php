<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\XcacheCache;

class XcacheCacheTest extends CacheTest{
    
    /**
     * @covers DBAL\Caching\XcacheCache
     */
    public function setUp() {
        if(!extension_loaded('xcache')) {
            $this->markTestSkipped(
                'The XCache extension is not available.'
            );
        }
        $this->cache = new XcacheCache();
        parent::setUp();
    }
    
}
