<?php
namespace DBAL\Tests;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
    public static $db;
    
    public function setUp(){
        self::$db = new \DBAL\Database('localhost', 'username', 'password', 'test_db', false, false, true, 'sqlite');
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        else{
            self::$db->query('CREATE TABLE `test_table` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `text_field` text NOT NULL,
    `number_field` int(11) NOT NULL,
    PRIMARY KEY (`id`)
);');
        }
    }
    
    public static function tearDownAfterClass(){
        self::$db = null;
    }

    public function testConnect(){
        $this->assertTrue(self::$db->isConnected());
    }
    
    public function testConnectFailure(){
        $db = new \DBAL\Database('localhost', 'wrong_username', 'incorrect_password', 'non_existent_db');
        $this->assertFalse($db->isConnected());
    }
    
    public function testQuery(){
        $query = self::$db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
        $this->assertArrayHasKey('0', $query);
        $this->assertCount(1, $query);
    }
    
    public function testSelect(){
        $simpleSelect = self::$db->select('test_table', array('id' => array('>', 1)), '*', array('id' => 'ASC'));
        $this->assertArrayHasKey('name', $simpleSelect);
    }
    
    public function testSelectAll(){
        $selectAll = self::$db->selectAll('test_table');
        $this->assertGreaterThan(1, self::$db->numRows());
        $this->assertArrayHasKey('id', $selectAll[0]);
    }
    
    public function testSelectFailure(){
        $this->assertFalse(self::$db->selectAll('test_table', array('id' => 100)));
        $this->assertFalse(self::$db->selectAll('unknown_table'));
    }
    
    public function testInsert(){
        $this->assertTrue(self::$db->insert('test_table', array('name' => 'Third User', 'text_field' => 'Helloooooo', 'number_field' => rand(1, 1000))));
    }
    
    public function testInsertFailure(){
        $this->assertFalse(self::$db->insert('test_table', array('id' => 3, 'name' => 'Third User', 'text_field' => NULL, 'number_field' => rand(1, 1000))));
    }
    
    public function testUpdate(){
        $this->assertTrue(self::$db->update('test_table', array('text_field' => 'Altered text', 'number_field' => rand(1, 1000)), array('id' => 3)));
    }
    
    public function testUpdateFailure(){
        $this->assertFalse(self::$db->update('test_table', array('number_field' => 256), array('id' => 1)));
    }
    
    public function testDelete(){
        $this->assertTrue(self::$db->delete('test_table', array('id' => array('>=', 3))));
    }
    
    public function testDeleteFailure(){
        $this->assertFalse(self::$db->delete('test_table', array('id' => 3)));
    }
    
    public function testCount(){
        $this->assertEquals(2, self::$db->count('test_table'));
    }
    
    public function testFulltextIndex(){
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    public function testLastInsertID(){
        $this->testInsert();
        $this->assertEquals(3, self::$db->lastInsertID());
    }
    
    public function testSetCaching(){
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
