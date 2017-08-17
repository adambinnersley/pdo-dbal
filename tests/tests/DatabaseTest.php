<?php
namespace DBAL\Tests;

use DBAL\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
    
    public static $db;
    
    /**
     * @covers DBAL\Database::__construct
     * @covers DBAL\Database::connectToServer
     */
    public static function setUpBeforeClass(){
        self::$db = new Database('db4free.net;port=3307', 'example_user_1', 'Password123', 'test_database_1');
    }
    
    /**
     * @covers DBAL\Database::__destruct
     */
    public static function tearDownAfterClass(){
        self::$db = null;
    }
    
    /**
     * @covers DBAL\Database::__construct
     * @covers DBAL\Database::connectToServer
     */
    public function testConnect(){
        $this->assertObjectHasAttribute('db', self::$db);
    }
    
    /**
     * @covers DBAL\Database
     */
    public function testConnectFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::query
     */
    public function testQuery(){
        $query = self::$db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
        $this->assertArrayHasKey('0', $query);
        $this->assertCount(1, $query);
    }
    
    /**
     * @covers DBAL\Database::select
     * @covers DBAL\Database::buildSelectQuery
     * @covers DBAL\Database::where
     * @covers DBAL\Database::orderBy
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     */
    public function testSelect(){
        $simpleSelect = self::$db->select('test_table', array('id' => array('>', 1)), '*', array('id' => 'ASC'));
        $this->assertArrayHasKey('name', $simpleSelect);
    }
    
    /**
     * @covers DBAL\Database::selectAll
     * @covers DBAL\Database::buildSelectQuery
     * @covers DBAL\Database::where
     * @covers DBAL\Database::orderBy
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     */
    public function testSelectAll(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::select
     * @covers DBAL\Database::selectAll
     * @covers DBAL\Database::buildSelectQuery
     * @covers DBAL\Database::where
     * @covers DBAL\Database::orderBy
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     */
    public function testSelectFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::insert
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testInsert(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::insert
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function tsetInsertFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::update
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testUpdate(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::update
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testUpdateFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::delete
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testDelete(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::delete
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testDeleteFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::count
     * @covers DBAL\Database::where
     * @covers DBAL\Database::executeQuery
     */
    public function testCount(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::fulltextIndex
     */
    public function testFulltextIndex(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::numRows
     * @covers DBAL\Database::rowCount
     */
    public function testNumRows(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::lastInsertId
     */
    public function testLastInsertID(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL|Database::setCaching
     */
    public function testCaching(){
        $this->assertFalse(false);
    }
}
