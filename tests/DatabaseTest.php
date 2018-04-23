<?php
namespace DBAL\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use DBAL\Caching\RedisCache;
use DBAL\Caching\MemcachedCache;

class DatabaseTest extends TestCase{
    public $db;
    
    protected $test_table = 'test_table';
    
    /**
     * @covers \DBAL\Database::__construct
     * @covers \DBAL\Database::connectToServer
     * @covers \DBAL\Database::isConnected
     */
    public function setUp(){
        $this->connectToLiveDB();
        if(!$this->db->isConnected()){
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        else{
            $this->db->query("DROP TABLE IF EXISTS `{$this->test_table}`;");
            $this->db->query("CREATE TABLE `{$this->test_table}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `text_field` text NOT NULL,
    `number_field` int(11) NOT NULL,
    PRIMARY KEY (`id`)
);");
            $this->db->insert($this->test_table, array('name' => 'My Name', 'text_field' => 'Hello World', 'number_field' => 256));
            $this->db->insert($this->test_table, array('name' => 'Inigo Montoya', 'text_field' => 'You killed my father, prepare to die', 'number_field' => 320));
        }
    }
    
    /**
     * @covers \DBAL\Database::__destruct
     * @covers \DBAL\Database::closeDatabase
     */
    public function tearDown(){
        $this->db = null;
    }
    
    /**
     * @covers \DBAL\Database::connectToServer
     * @covers \DBAL\Database::isConnected
     */
    public function testConnect(){
        $this->assertTrue($this->db->isConnected());
    }
    
    /**
     * @covers \DBAL\Database::__destruct
     * @covers \DBAL\Database::closeDatabase
     */
    public function testCloseDatabaseConnection(){
        $this->assertTrue($this->db->isConnected());
        $this->assertObjectHasAttribute('sql', $this->db);
        $this->db = null;
        $this->assertNull($this->db);
        $this->connectToLiveDB();
    }
    
    /**
     * @covers \DBAL\Database::__construct
     * @covers \DBAL\Database::connectToServer
     * @covers \DBAL\Database::isConnected
     * @covers \DBAL\Database::error
     */
    public function testConnectFailure(){
        $db = new Database('localhost', 'wrong_username', 'incorrect_password', 'non_existent_db');
        $this->assertFalse($db->isConnected());
        $this->connectToLiveDB();
    }
    
    /**
     * @covers \DBAL\Database::query
     */
    public function testQuery(){
        // Insert a couple of test vales
        $this->db->insert($this->test_table, array('name' => 'My Name', 'text_field' => 'Hello World', 'number_field' => rand(1, 1000)));
        $this->db->insert($this->test_table, array('name' => 'Inigo Montoya', 'text_field' => 'You killed my father, prepare to die', 'number_field' => rand(1, 1000)));
        $query = $this->db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
        $this->assertArrayHasKey(0, $query);
        $this->assertCount(1, $query);
    }
    
    /**
     * @covers \DBAL\Database::select
     * @covers \DBAL\Database::selectAll
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::orderBy
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testSelect(){
       $simpleSelect = $this->db->select($this->test_table, array('id' => array('>', 1)), '*', array('id' => 'ASC'));
       $this->assertArrayHasKey('name', $simpleSelect);
       $this->assertFalse($this->db->select($this->test_table, array('id' => 'IS NULL'), '*', array('id' => 'ASC')));
       $this->assertArrayHasKey('name', $this->db->select($this->test_table, array('id' => 'IS NOT NULL'), array('id', 'name'), array('id' => 'ASC')));
       $between = $this->db->select($this->test_table, array('id' => array('BETWEEN' => array(2, 3))), '*', array('id' => 'ASC'));
       $this->assertEquals(2, $between['id']);
    }
    
    /**
     * @covers \DBAL\Database::selectAll
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::rowCount
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::limit
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testSelectAll(){
        $this->assertEquals(1, $this->db->numRows());
        $selectAll = $this->db->selectAll($this->test_table);
        $this->assertGreaterThan(1, $this->db->numRows());
        $this->assertArrayHasKey('id', $selectAll[0]);
        $this->db->selectAll($this->test_table, array(), '*', array(), 1);
        $this->assertEquals(1, $this->db->rowCount());
        for($i = 1; $i <= 200; $i++){
            // Insert some more values for testing
            $this->db->insert($this->test_table, array('name' => 'Name '.$i, 'text_field' => 'TextField'.$i, 'number_field' => rand(1, 1000)));
        }
        $original = $this->db->selectAll($this->test_table, array(), '*', array(), array(0 => 50));
        $this->assertLessThan(51, $this->db->numRows());
        $random = $this->db->selectAll($this->test_table, array(), '*', 'RAND()', array(0 => 50));
        $this->assertNotEquals($original, $random);
    }
    
    /**
     * @covers \DBAL\Database::selectAll
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Database::error
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testSelectFailure(){
        $this->assertFalse($this->db->selectAll($this->test_table, array('id' => 100)));
        $this->assertFalse($this->db->selectAll('unknown_table'));
    }
    
    /**
     * @covers \DBAL\Database::fetchColumn
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testFetchColumn(){
        $this->assertEquals('Inigo Montoya', $this->db->fetchColumn($this->test_table, array('id' => 2), '*', 1));
    }
    
    /**
     * @covers \DBAL\Database::fetchColumn
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testFetchColumnFailure(){
        $this->assertFalse($this->db->fetchColumn($this->test_table, array('id' => 2), '*', 6));
    }


    /**
     * @covers \DBAL\Database::insert
     * @covers \DBAL\Database::fields
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     */
    public function testInsert(){
        $this->assertTrue($this->db->insert($this->test_table, array('name' => 'Third User', 'text_field' => 'Helloooooo', 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers \DBAL\Database::insert
     * @covers \DBAL\Database::fields
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Database::error
     */
    public function testInsertFailure(){
        $this->assertFalse($this->db->insert($this->test_table, array('id' => 3, 'name' => 'Third User', 'text_field' => NULL, 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers \DBAL\Database::update
     * @covers \DBAL\Database::fields
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testUpdate(){
        $this->assertTrue($this->db->update($this->test_table, array('text_field' => 'Altered text', 'number_field' => rand(1, 1000)), array('id' => 1)));
    }
    
    /**
     * @covers \DBAL\Database::update
     * @covers \DBAL\Database::fields
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Database::error
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testUpdateFailure(){
        $this->assertFalse($this->db->update($this->test_table, array('number_field' => 256), array('id' => 1)));
    }
    
    /**
     * @covers \DBAL\Database::delete
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testDelete(){
        $this->assertTrue($this->db->delete($this->test_table, array('id' => array('>=', 2))));
    }
    
    /**
     * @covers \DBAL\Database::delete
     * @covers \DBAL\Database::where
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\Operators::getOperatorFormat
     * @covers \DBAL\Modifiers\Operators::isOperatorValid
     * @covers \DBAL\Modifiers\Operators::isOperatorPrepared
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testDeleteFailure(){
        $this->assertFalse($this->db->delete($this->test_table, array('id' => 3)));
    }
    
    /**
     * @covers \DBAL\Database::count
     * @covers \DBAL\Database::executeQuery
     */    
    public function testCount(){
        $this->assertEquals(2, $this->db->count($this->test_table));
    }
    
    /**
     * @covers \DBAL\Database::lastInsertID
     */
    public function testLastInsertID(){
        $this->testInsert();
        $this->assertEquals(3, $this->db->lastInsertID());
    }
    
    /**
     * @covers \DBAL\Database::serverVersion
     */
    public function testServerVersion(){
        $this->assertGreaterThan(5, $this->db->serverVersion());
        $this->assertContains('.', $this->db->serverVersion());
    }
    
    /**
     * @covers \DBAL\Database::setCaching
     * @covers \DBAL\Caching\RedisCache
     * @covers \DBAL\Caching\RedisCache::__construct
     * @covers \DBAL\Caching\RedisCache::connect
     */
    public function testSetCaching(){
        if (extension_loaded('redis')) {
            $caching = new RedisCache();
            $caching->connect('127.0.0.1', 6379);
            $this->db->setCaching($caching);
        }
        $this->assertObjectHasAttribute('sql', $this->db->setCaching('not_a_instance_od_cache_but_should_still_return'));
    }
    
    /**
     * @covers \DBAL\Database::truncate
     * @covers \DBAL\Database::error
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Database::numRows
     */
    public function testTruncate(){
        $this->db->truncate($this->test_table);
        $this->assertEquals(0, $this->db->count($this->test_table));
        $this->assertFalse($this->db->truncate('any_table'));
    }
    
    /**
     * @covers \DBAL\Database::setLogLocation
     */
    public function testChangeLogLocation(){
        $this->assertObjectHasAttribute('sql', $this->db->setLogLocation('test/logs/'));
        $this->assertObjectHasAttribute('sql', $this->db->setLogLocation(1));
        $this->assertObjectHasAttribute('sql', $this->db->setLogLocation(false));
    }
    
    /**
     * @covers \DBAL\Database::setCache
     * @covers \DBAL\Database::getCache
     * @covers \DBAL\Caching\RedisCache
     * @covers \DBAL\Caching\RedisCache::__construct
     * @covers \DBAL\Caching\RedisCache::connect
     * @covers \DBAL\Caching\RedisCache::save
     * @covers \DBAL\Caching\RedisCache::fetch
     */
    public function testRedisSetCache(){
        $loaded = false;
        if (extension_loaded('redis')) {
            $caching = new RedisCache();
            $caching->connect('127.0.0.1', 6379);
            $this->db->setCaching($caching);
            $this->db->setCache('cache_status', 'Success');
            $loaded = ($this->db->getCache('cache_status') === 'Success' ? true : false);
        }
        if($loaded === true) {
            $this->assertEmpty($this->db->setCache('mykey', 'Hello'));
            $this->assertEquals('Hello', $this->db->getCache('mykey'));
            $this->assertEmpty($this->db->getCache('another_key_name'));
        }
        else{
            $this->assertEmpty($this->db->setCache('mykey', 'Hello'));
            $this->assertFalse($this->db->getCache('mykey'));
            $this->assertFalse($this->db->getCache('another_key_name'));
        }
    }
    
    
    /**
     * @covers \DBAL\Database::setCache
     * @covers \DBAL\Database::getCache
     * @covers \DBAL\Caching\MemcachedCache
     * @covers \DBAL\Caching\MemcachedCache::__construct
     * @covers \DBAL\Caching\MemcachedCache::connect
     * @covers \DBAL\Caching\MemcachedCache::save
     * @covers \DBAL\Caching\MemcachedCache::fetch
     */
    public function testMemcachedSetCache(){
        $loaded = false;
        if (extension_loaded('memcached')) {
            $caching = new MemcachedCache();
            $caching->connect('127.0.0.1', 11211);
            $this->db->setCaching($caching);
            $this->db->setCache('cache_status', 'Success');
            $loaded = ($this->db->getCache('cache_status') === 'Success' ? true : false);
        }
        if($loaded === true) {
            $this->assertEmpty($this->db->setCache('mykey', 'Hello'));
            $this->assertEquals('Hello', $this->db->getCache('mykey'));
            $this->assertEmpty($this->db->getCache('another_key_name'));
        }
        else{
            $this->assertEmpty($this->db->setCache('mykey', 'Hello'));
            $this->assertFalse($this->db->getCache('mykey'));
            $this->assertFalse($this->db->getCache('another_key_name'));
        }
    }
    
    protected function connectToLiveDB(){
        $this->db = new Database($GLOBALS['HOSTNAME'], $GLOBALS['USERNAME'], $GLOBALS['PASSWORD'], $GLOBALS['DATABASE'], '127.0.0.1', false, true, $GLOBALS['DRIVER']);
    }
}
