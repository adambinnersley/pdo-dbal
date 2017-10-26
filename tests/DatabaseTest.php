<?php
namespace DBAL\Tests;

use DBAL\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
    public static $db;
    
    /**
     * @covers Database::__construct
     * @covers Database::connectToServer
     */
    public function setUp(){
        self::$db = new Database('localhost', 'username', 'password', 'test_db', false, false, true, 'sqlite');
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
    }
    
    /**
     * @covers Database::__destruct
     */
    public static function tearDownAfterClass(){
        self::$db = null;
    }

    /**
     * @covers Database::__construct
     * @covers Database::connectToServer
     */
    public function testConnect(){
        $this->assertTrue(self::$db->isConnected());
    }
    
    /**
     * @covers Database
     */
    public function testConnectFailure(){
        $db = new Database('localhost', 'wrong_username', 'incorrect_password', 'non_existent_db');
        $this->assertFalse($db->isConnected());
    }
    
    /**
     * @covers Database::query
     */
    public function testQuery(){
        $query = self::$db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
        $this->assertArrayHasKey('0', $query);
        $this->assertCount(1, $query);
    }
    
    /**
     * @covers Database::select
     * @covers Database::buildSelectQuery
     * @covers Database::where
     * @covers Database::orderBy
     * @covers Database::limit
     * @covers Database::executeQuery
     */
    public function testSelect(){
        $simpleSelect = self::$db->select('test_table', array('id' => array('>', 1)), '*', array('id' => 'ASC'));
        $this->assertArrayHasKey('name', $simpleSelect);
    }
    
    /**
     * @covers Database::selectAll
     * @covers Database::buildSelectQuery
     * @covers Database::where
     * @covers Database::orderBy
     * @covers Database::limit
     * @covers Database::executeQuery
     */
    public function testSelectAll(){
        $selectAll = self::$db->selectAll('test_table');
        $this->assertGreaterThan(1, self::$db->numRows());
        $this->assertArrayHasKey('id', $selectAll[0]);
    }
    
    /**
     * @covers Database::select
     * @covers Database::selectAll
     * @covers Database::buildSelectQuery
     * @covers Database::where
     * @covers Database::orderBy
     * @covers Database::limit
     * @covers Database::executeQuery
     */
    public function testSelectFailure(){
        $this->assertFalse(self::$db->selectAll('test_table', array('id' => 100)));
        $this->assertFalse(self::$db->selectAll('unknown_table'));
    }
    
    /**
     * @covers Database::insert
     * @covers Database::fields
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testInsert(){
        $this->assertTrue(self::$db->insert('test_table', array('name' => 'Third User', 'text_field' => 'Helloooooo', 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers Database::insert
     * @covers Database::fields
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testInsertFailure(){
        $this->assertFalse(self::$db->insert('test_table', array('id' => 3, 'name' => 'Third User', 'text_field' => NULL, 'number_field' => rand(1, 1000))));
    }
    
    /**
     * @covers Database::update
     * @covers Database::fields
     * @covers Database::where
     * @covers Database::limit
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testUpdate(){
        $this->assertTrue(self::$db->update('test_table', array('text_field' => 'Altered text', 'number_field' => rand(1, 1000)), array('id' => 3)));
    }
    
    /**
     * @covers Database::update
     * @covers Database::fields
     * @covers Database::where
     * @covers Database::limit
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testUpdateFailure(){
        $this->assertFalse(self::$db->update('test_table', array('number_field' => 256), array('id' => 1)));
    }
    
    /**
     * @covers Database::delete
     * @covers Database::where
     * @covers Database::limit
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testDelete(){
        $this->assertTrue(self::$db->delete('test_table', array('id' => array('>=', 3))));
    }
    
    /**
     * @covers Database::delete
     * @covers Database::where
     * @covers Database::limit
     * @covers Database::executeQuery
     * @covers Database::numRows
     */
    public function testDeleteFailure(){
        $this->assertFalse(self::$db->delete('test_table', array('id' => 3)));
    }
    
    /**
     * @covers Database::count
     * @covers Database::where
     * @covers Database::executeQuery
     */
    public function testCount(){
        $this->assertEquals(2, self::$db->count('test_table'));
    }
    
    /**
     * @covers Database::fulltextIndex
     */
    public function testFulltextIndex(){
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    /**
     * @covers Database::lastInsertId
     */
    public function testLastInsertID(){
        $this->testInsert();
        $this->assertEquals(3, self::$db->lastInsertID());
    }
    
    /**
     * @covers Database::setCaching
     */
    public function testSetCaching(){
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
