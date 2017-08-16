<?php
namespace DBAL\Tests;

use DBAL\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
    
    public $db;
    
    /**
     * @covers DBAL\Database
     */
    public function setUp(){
        $this->db = new Database('localhost', 'root', '', 'test_db');
    }
    
    /**
     * @covers DBAL\Database
     */
    public function tearDown(){
        $this->db = null;
    }
    
    /**
     * @covers DBAL\Database
     */
    public function testConnect(){
        $this->assertObjectHasAttribute('db', $this->db);
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
        $query = $this->db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
        $this->assertArrayHasKey('0', $query);
        $this->assertCount(1, $query);
    }
    
    /**
     * @covers DBAL\Database::select
     */
    public function testSelect(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::selectAll
     */
    public function testSelectAll(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::selectAll
     */
    public function testSelectFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::insert
     */
    public function testInsert(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::insert
     */
    public function tsetInsertFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::update
     */
    public function testUpdate(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::update
     */
    public function testUpdateFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::delete
     */
    public function testDelete(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::delete
     */
    public function testDeleteFailure(){
        $this->assertFalse(false);
    }
    
    /**
     * @covers DBAL\Database::count
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
}
