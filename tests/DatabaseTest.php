<?php
namespace DBAL\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
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
    }
    
    /**
     * @covers \DBAL\Database::selectAll
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::rowCount
     * @covers \DBAL\Database::limit
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testSelectAll(){
        $selectAll = $this->db->selectAll($this->test_table);
        $this->assertGreaterThan(1, $this->db->numRows());
        $this->assertArrayHasKey('id', $selectAll[0]);
        $this->db->selectAll($this->test_table, array(), '*', array(), 1);
        $this->assertEquals(1, $this->db->rowCount());
    }
    
    /**
     * @covers \DBAL\Database::selectAll
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
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
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testFetchColumn(){
        $this->assertEquals('Inigo Montoya', $this->db->fetchColumn($this->test_table, array('id' => 2), '*', 1));
    }
    
    /**
     * @covers \DBAL\Database::fetchColumn
     * @covers \DBAL\Database::buildSelectQuery
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     * @covers \DBAL\Modifiers\SafeString::makeSafe
     */
    public function testFetchColumnFailure(){
        $this->assertFalse($this->db->fetchColumn($this->test_table, array('id' => 2), '*', 6));
    }


    /**
     * @covers \DBAL\Database::insert
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     */
    public function testInsert(){
        $this->assertTrue($this->db->insert($this->test_table, array('name' => 'Third User', 'text_field' => 'Helloooooo', 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers \DBAL\Database::insert
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     */
    public function testInsertFailure(){
        $this->assertFalse($this->db->insert($this->test_table, array('id' => 3, 'name' => 'Third User', 'text_field' => NULL, 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers \DBAL\Database::update
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     */
    public function testUpdate(){
        $this->assertTrue($this->db->update($this->test_table, array('text_field' => 'Altered text', 'number_field' => rand(1, 1000)), array('id' => 1)));
    }
    
    /**
     * @covers \DBAL\Database::update
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
     */
    public function testUpdateFailure(){
        $this->assertFalse($this->db->update($this->test_table, array('number_field' => 256), array('id' => 1)));
    }
    
    /**
     * @covers \DBAL\Database::delete
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::formatValues
     * @covers \DBAL\Database::executeQuery
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
     * @covers \DBAL\Database::numRows
     * @covers \DBAL\Database::executeQuery
     * @covers \DBAL\Database::bindValues
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
     * @covers \DBAL\Database::setCaching
     */
    public function testSetCaching(){
        $caching = new MemcachedCache();
        if(is_object($caching)){
            $this->db->setCaching($caching);
        }
    }
    
    protected function connectToLiveDB(){
        $this->db = new Database($GLOBALS['HOSTNAME'], $GLOBALS['USERNAME'], $GLOBALS['PASSWORD'], $GLOBALS['DATABASE'], false, false, true, $GLOBALS['DRIVER']);
    }
}
