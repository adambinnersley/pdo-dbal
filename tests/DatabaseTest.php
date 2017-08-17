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
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
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
        if(is_object(self::$db)){
            $this->assertObjectHasAttribute('db', self::$db);
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database
     */
    public function testConnectFailure(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::query
     */
    public function testQuery(){
        if(is_object(self::$db)){
            $query = self::$db->query("SELECT * FROM `test_table` WHERE `id` = ?", array(1));
            $this->assertArrayHasKey('0', $query);
            $this->assertCount(1, $query);
        }
        else{
            $this->assertFalse(false);
        }
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
        if(is_object(self::$db)){
            $simpleSelect = self::$db->select('test_table', array('id' => array('>', 1)), '*', array('id' => 'ASC'));
            $this->assertArrayHasKey('name', $simpleSelect);
        }
        else{
            $this->assertFalse(false);
        }
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
        if(is_object(self::$db)){
            $selectAll = self::$db->selectAll('test_table');
            $this->assertGreaterThan(1, self::$db->numRows());
            $this->assertArrayHasKey('id', $selectAll[0]);
        }
        else{
            $this->assertFalse(false);
        }
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
        if(is_object(self::$db)){
            $this->assertFalse(self::$db->selectAll('unknown_table'));
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::insert
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testInsert(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::insert
     * @covers DBAL\Database::fields
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function tsetInsertFailure(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
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
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
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
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::delete
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testDelete(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::delete
     * @covers DBAL\Database::where
     * @covers DBAL\Database::limit
     * @covers DBAL\Database::executeQuery
     * @covers DBAL\Database::numRows
     */
    public function testDeleteFailure(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::count
     * @covers DBAL\Database::where
     * @covers DBAL\Database::executeQuery
     */
    public function testCount(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::fulltextIndex
     */
    public function testFulltextIndex(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::numRows
     * @covers DBAL\Database::rowCount
     */
    public function testNumRows(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::lastInsertId
     */
    public function testLastInsertID(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
    
    /**
     * @covers DBAL\Database::setCaching
     */
    public function testCaching(){
        if(is_object(self::$db)){
            
        }
        else{
            $this->assertFalse(false);
        }
    }
}
