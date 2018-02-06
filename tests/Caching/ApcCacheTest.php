<?php
namespace DBAL\Tests\Caching;

use DBAL\Caching\ApcCache;

class ApcCacheTest extends CacheTest{
    
    /**
     * @covers DBAL\Caching\ApcCache
     */
    public function setUp() {
        if(!extension_loaded('apc')) {
            $this->markTestSkipped(
                'The APC extension is not available.'
            );
        }
        $this->cache = new ApcCache();
        parent::setUp();
    }
    
}
