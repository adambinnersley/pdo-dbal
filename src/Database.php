<?php
namespace DBAL;

use PDO;

/**
 * PDO Database connection class
 *
 * @author Adam Binnersley <abinnersley@gmail.com>
 * @version PDO Database Class
 */
final class Database implements DBInterface{
    protected $db;
    protected $sql;
    private $key;
    
    protected $logLocation = 'logs'.DIRECTORY_SEPARATOR;
    public $logErrors = true;
    public $logQueries = false;
    public $displayErrors = false;
    
    protected $database;
    protected $cacheEnabled = false;
    protected $cacheObj;
    protected $cacheValue;
    protected $modified = false;

    private $query;
    private $values = [];
    private $prepare = [];

    /**
     * Connect to database using PDO connection
     * @param string $hostname This should be the host of the database e.g. 'localhost'
     * @param string $username This should be the username for the chosen database
     * @param string $password This should be the password for the chosen database 
     * @param string $database This should be the database that you wish to connect to
     * @param string|false $backuphost If you have a replication server set up put the hostname or IP address incase the primary server goes down
     * @param object|false $cache If you want to cache the queries with Memcache(d)/Redis/APC/Xcache This should be the object else set to false
     * @param int $port This should be the port number of the MySQL database connection
     */
    public function __construct($hostname, $username, $password, $database, $backuphost = false, $cache = false, $port = 3306){
        try{
            $this->connectToServer($username, $password, $database, $hostname, $port);
        }
        catch(\Exception $e){
            if($backuphost !== false){
                $this->connectToServer($username, $password, $database, $backuphost, $port);
            }
            $this->error($e);
        }
        if(is_object($cache)){
            $this->setCaching($cache);
        }
    }
    
    /**
     * Closes the PDO database connection when Database object unset
     */
    public function __destruct(){
        $this->closeDatabase();
    }
    
    /**
     * Connect to the database using PDO connection
     * @param string $username This should be the username for the chosen database
     * @param string $password This should be the password for the chosen database 
     * @param string $database This should be the database that you wish to connect to
     * @param string $hostname The hostname for the database
     * @param int $port The port number to connect to the MySQL server
     */
    protected function connectToServer($username, $password, $database, $hostname, $port = 3306){
        if(!$this->db){
            $this->database = $database;
            $this->db = new PDO('mysql:host='.$hostname.';port='.$port.';dbname='.$database, $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::ATTR_PERSISTENT => true, PDO::ATTR_EMULATE_PREPARES => true));
        }
    }
    
    /**
     * Enables the caching and set the caching object to the one provided
     * @param object $caching This should be class of the type of caching you are using
     */
    public function setCaching($caching){
        if(is_object($caching)){
            $this->cacheObj = $caching;
            $this->cacheEnabled = true;
        }
        return $this;
    }
    
    /**
     * This query function is used for more advanced SQL queries for which non of the other methods fit
     * @param string $sql This should be the SQL query which you wish to run
     * @return array Returns array of results for the query that has just been run
     */
    public function query($sql, $variables = array(), $cache = true){
        try{
            $this->sql = $sql;
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute($variables);
            if(strpos($this->sql, 'SELECT') !== false){
                return $this->query->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        catch(\Exception $e){
            $this->error($e);
        }
    }
    
    /**
     * Returns a single record for a select query for the chosen table
     * @param string $table This should be the table you wish to select the values from
     * @param array $where Should be the field names and values you wish to use as the where query e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param string|array $fields This should be the records you wis to select from the table. It should be either set as '*' which is the default or set as an array in the following format array('field', 'field2', 'field3', etc).
     * @param array $order This is the order you wish the results to be ordered in should be formatted as follows array('fieldname' => 'ASC') or array("'fieldname', 'fieldname2'" => 'DESC')
     * @param boolean $cache If the query should be cached or loaded from cache set to true else set to false
     * @return array Returns a single table record as the standard array when running SQL queries
     */
    public function select($table, $where = array(), $fields = '*', $order = array(), $cache = true){
        return $this->selectAll($table, $where, $fields, $order, 1, $cache);
    }
    
    /**
     * Returns a multidimensional array of the results from the selected table given the given parameters
     * @param string $table This should be the table you wish to select the values from
     * @param array $where Should be the field names and values you wish to use as the where query e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param string|array $fields This should be the records you wis to select from the table. It should be either set as '*' which is the default or set as an array in the following format array('field', 'field2', 'field3', etc).
     * @param array $order This is the order you wish the results to be ordered in should be formatted as follows array('fieldname' => 'ASC') or array("'fieldname', 'fieldname2'" => 'DESC')
     * @param integer|array $limit The number of results you want to return 0 is default and returns all results, else should be formated either as a standard integer or as an array as the start and end values e.g. array(0 => 150)
     * @param boolean $cache If the query should be cached or loaded from cache set to true else set to false
     * @return array Returns a multidimensional array with the chosen fields from the table
     */
    public function selectAll($table, $where = array(), $fields = '*', $order = array(), $limit = 0, $cache = true){        
        $this->buildSelectQuery($table, $where, $fields, $order, $limit);
        $result = $this->executeQuery($cache);
        if(!$result){
            if($limit === 1){$result = $this->query->fetch(PDO::FETCH_ASSOC);} // Reduce the memory usage if only one record and increase performance
            else{$result = $this->query->fetchAll(PDO::FETCH_ASSOC);}
            if($cache && $this->cacheEnabled){$this->setCache($this->key, $result);}
        }
        return $result ? $result : false;
    }
    
    /**
     * Returns a single column value for a given query
     * @param string $table This should be the table you wish to select the values from
     * @param array $where Should be the field names and values you wish to use as the where query e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param array $fields This should be the records you wis to select from the table. It should be either set as '*' which is the default or set as an array in the following format array('field', 'field2', 'field3', etc).
     * @param int $colNum This should be the column number you wish to get (starts at 0)
     * @param array $order This is the order you wish the results to be ordered in should be formatted as follows array('fieldname' => 'ASC') or array("'fieldname', 'fieldname2'" => 'DESC') so it can be done in both directions
     * @param boolean $cache If the query should be cached or loaded from cache set to true else set to false
     * @return mixed If a result is found will return the value of the colum given else will return false
     */
    public function fetchColumn($table, $where = array(), $fields = '*', $colNum = 0, $order = array(), $cache = true){
        $this->buildSelectQuery($table, $where, $fields, $order, 1);
        $result = $this->executeQuery($cache);
        if(!$result){
            $result = $this->query->fetchColumn(intval($colNum));
            if($cache && $this->cacheEnabled){$this->setCache($this->key, $result);}
        }
        return $result;
    }
    
    /**
     * Inserts into database using the prepared PDO statements 
     * @param string $table This should be the table you wish to insert the values into
     * @param array $records This should be the field names and values in the format of array('fieldname' => 'value', 'fieldname2' => 'value2', etc.)
     * @return boolean If data is inserted returns true else returns false
     */
    public function insert($table, $records){
        unset($this->prepare);
        
        $this->sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s);", $table, $this->fields($records, true), implode(', ', $this->prepare));
        $this->executeQuery(false);
        return $this->numRows() ? true : false;
    }
    
    /**
     * Updates values in a database using the provide variables
     * @param string $table This should be the table you wish to update the values for
     * @param array $records This should be the field names and new values in the format of array('fieldname' => 'newvalue', 'fieldname2' => 'newvalue2', etc.)
     * @param array $where Should be the field names and values you wish to update in the form of an array e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param int $limit The number of results you want to return 0 is default and will update all results that match the query, else should be formated as a standard integer
     * @return boolean Returns true if update is successful else returns false
     */
    public function update($table, $records, $where = array(), $limit = 0){
        $this->sql = sprintf("UPDATE `%s` SET %s %s%s;", $table, $this->fields($records), $this->where($where), $this->limit($limit));
        $this->executeQuery(false);
        return $this->numRows() ? true : false;
    }
    
    /**
     * Deletes records from the given table based on the variables given
     * @param string $table This should be the table you wish to delete the records from
     * @param array $where This should be an array of for the where statement
     * @param int $limit The number of results you want to return 0 is default and will delete all results that match the query, else should be formated as a standard integer
     */
    public function delete($table, $where, $limit = 0){
        $this->sql = sprintf("DELETE FROM `%s` %s%s;", $table, $this->where($where), $this->limit($limit));
        $this->executeQuery(false);
        return $this->numRows() ? true : false;
    }
    
    /**
     * Count the number of return results 
     * @param string $table The table you wish to count the result of 
     * @param array $where Should be the field names and values you wish to use as the where query e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param boolean $cache If the query should be cached or loaded from cache set to true else set to false
     * @return int Returns the number of results
     */
    public function count($table, $where = array(), $cache = true){
        $this->sql = sprintf("SELECT count(*) FROM `%s`%s;", $table, $this->where($where));
        $this->key = md5($this->database.$this->sql.serialize($this->values));
        
        $result = $this->executeQuery($cache);
        if(!$result){
            $result = $this->query->fetchColumn();
            if($cache && $this->cacheEnabled){$this->setCache($this->key, $result);}
        }
        return $result;
    }
    
    /**
     * Truncates a given table from the selected database so there are no values in the table
     * @param string $table This should be the table you wish to truncate
     * @return boolean If the table is emptied returns true else returns false
     */
    public function truncate($table){
        try{
            $this->sql = sprintf("TRUNCATE TABLE `%s`", $table);
            $this->query = $this->db->exec($this->sql);
        }
        catch(\Exception $e){
            $this->error($e);
        }
        return $this->query ? true : false;
    }
    
    /**
     * Returns the number of rows for the last query sent
     * @return int Returns the number of rows for the last query
     */
    public function numRows(){
        if(isset($this->query)){
            return $this->query->rowCount();
        }
        return 0;
    }
    
    /**
     * Returns the number of rows for the last query sent (Looks a the numRows() function just added incase of habbit)
     * @return int Returns the number of rows for the last query
     */
    public function rowCount(){
        return $this->numRows();
    }
    
    /**
     * Returns the ID of the last record last inserted 
     * @param string $name This should be the name of the sequence object you wish to retrieve
     * @return int|string Returns the last inserted ID of the last insert item if $name is null else returns string with sequenced object
     */
    public function lastInsertId($name = null) {
        return $this->db->lastInsertId($name);
    }
    
    /**
     * Returns the index of the given table or tables within the database
     * @param string|array $table Table can wither be a standard string with a single table name or an array with multiple table names
     * @return array Returns the table index for the selected table as an array 
     */
    public function fulltextIndex($table){
        $fieldlist = array();
        if(is_array($table)){
            foreach($table as $name){
                $fieldlist[$name] = $this->fulltextIndex($name);
            }
        }else{
            try{
                $this->query = $this->db->prepare("SHOW INDEX FROM ?;");
                $this->query->execute($table);
            }
            catch(\Exception $e){
                $this->error($e);
            }
            
            while($index = $this->query->fetchAll(PDO::FETCH_ASSOC)){
                if($index['Index_type'] == 'FULLTEXT' && $index['Key_name'] == 'fulltext'){
                    $fieldlist[] = $index['Column_name'];
                }
            }
        }
        return $fieldlist;
    }
    
    /**
     * Checks to see if a connection has been made to the server
     * @return boolean
     */
    public function isConnected(){
        return is_object($this->db) ? true : false;
    }
    
    /**
     * Returns the server version information
     */
    public function serverVersion(){
        return $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
    }
    
    /**
     * Displays the error massage which occurs
     * @param \Exception $error This should be an instance of Exception
     */
    private function error($error){
        if($this->logErrors){
            $file = $this->logLocation.'db-errors.txt';
            $current = file_get_contents($file);
            $current .= date('d/m/Y H:i:s')." ERROR: ".$error->getMessage()." on ".$this->sql."\n";
            file_put_contents($file, $current);
        }
        if($this->displayErrors){
            die('ERROR: '.$error->getMessage().' on '.$this->sql);
        }
    }
    
    /**
     * Writes all queries to a log file
     */
    public function writeQueryToLog(){
        $file = $this->logLocation.'queries.txt';
        $current = file_get_contents($file);
        $current .= "SQL: ".$this->sql.":".serialize($this->values)."\n";
        file_put_contents($file, $current);
    }
    
    /**
     * Closes the PDO database connection by setting the connection to NULL 
     */
    public function closeDatabase(){
        $this->db = null;
    }
    
    /**
     * Build the SQL query but doesn't execute it
     * @param string $table This should be the table you wish to select the values from
     * @param array $where Should be the field names and values you wish to use as the where query e.g. array('fieldname' => 'value', 'fieldname2' => 'value2', etc).
     * @param string|array $fields This should be the records you wis to select from the table. It should be either set as '*' which is the default or set as an array in the following format array('field', 'field2', 'field3', etc).
     * @param array $order This is the order you wish the results to be ordered in should be formatted as follows array('fieldname' => 'ASC') or array("'fieldname', 'fieldname2'" => 'DESC') so it can be done in both directions
     * @param integer|array $limit The number of results you want to return 0 is default and returns all results, else should be formated either as a standard integer or as an array as the start and end values e.g. array(0 => 150)
     */
    protected function buildSelectQuery($table, $where = array(), $fields = '*', $order = array(), $limit = 0){
        if(is_array($fields)){
            $selectfields = array();
            foreach($fields as $field => $value){
                $selectfields[] = sprintf("`%s`", $value);
            }
            $fieldList = implode(', ', $selectfields);
        }
        else{$fieldList = '*';}
        
        $this->sql = sprintf("SELECT %s FROM `%s`%s%s%s;", $fieldList, $table, $this->where($where), $this->orderBy($order), $this->limit($limit));
        $this->key = md5($this->database.$this->sql.serialize($this->values));
    }
    
    /**
     * Execute the current query if no cache value is available
     * @param boolean $cache If the cache should be checked for the checked for the values of the query set to true else set to false 
     * @return mixed If a cached value exists will be returned else if cache is not checked and query is executed will not return anything
     */
    protected function executeQuery($cache = true){
        if($this->logQueries){$this->writeQueryToLog();}
        if($cache && $this->cacheEnabled && $this->getCache($this->key)){
            return $this->cacheValue;
        }
        try{
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute($this->values);
            unset($this->values);
            $this->values = [];
        }
        catch(\Exception $e){
            $this->error($e);
        }
}
	
    /**
     * This outputs the SQL where query based on a given array
     * @param array $where This should be an array that you wish to create the where query for in the for array('field1' => 'test') or array('field1' => array('>=', 0))
     * @return string|false If the where query is an array will return the where string and set the values else returns false if no array sent
     */
    private function where($where){
        if(is_array($where) && !empty($where)){
            $wherefields = array();
            foreach($where as $what => $value){
                if(is_array($value)){
                    if($value[1] == 'NULL' || $value[1] == 'NOT NULL'){
                        $wherefields[] = sprintf("`%s` %s %s", $what, addslashes($value[0]), $value[1]);
                    }
                    else{
                        $wherefields[] = sprintf("`%s` %s ?", $what, addslashes($value[0]));
                        $this->values[] = $value[1];
                    }
                }
                else{
                    $wherefields[] = sprintf("`%s` = ?", $what);
                    $this->values[] = $value;
                }
            }
            if(!empty($wherefields)){
                return " WHERE ".implode(' AND ', $wherefields);
            }
        }
        return false;
    }
    
    /**
     * Sets the order sting for the SQL query based on an array or string
     * @param array|string $order This should be either set to array('fieldname' => 'ASC/DESC') or RAND()
     * @return string|false If the SQL query has an valid order by will return a string else returns false
     */
    private function orderBy($order){
        if(is_array($order) && !empty(array_filter($order))){
            foreach($order as $fieldorder => $fieldvalue){
                return sprintf(" ORDER BY `%s` %s", $fieldorder, strtoupper($fieldvalue));
            }
        }
        elseif($order == 'RAND()'){
            return " ORDER BY RAND()";
        }
        return false;
    }
    
    /**
     * Build the field list for the query
     * @param array $records This should be an array listing all of the fields
     * @param boolean $insert If this is an insert statement should be set to true to create the correct amount of queries for the prepared statement
     * @return string The fields list will be returned as a string to insert into the SQL query
     */
    private function fields($records, $insert = false){
        $fields = array();
        
        foreach($records as $field => $value){
            if($insert === true){
                $fields[] = sprintf("`%s`", $field);
                $this->prepare[] = '?';
            }
            else{
                $fields[] = sprintf("`%s` = ?", $field);
            }
            $this->values[] = $value;
        }
        return implode(', ', $fields);
    }
    
    /**
     * Returns the limit SQL for the current query as a string
     * @param integer|array $limit This should either be set as an integer or should be set as an array with a start and end value  
     * @return string|false Will return the LIMIT string for the current query if it is valid else returns false
     */
    private function limit($limit = 0){
        if(is_array($limit) && !empty(array_filter($limit))){
            foreach($limit as $start => $end){
                 return " LIMIT ".(int)$start.", ".(int)$end;
            }
        }
        elseif((int)$limit > 0){
            return " LIMIT ".(int)$limit;
        }
        return false;
    }
    
    
    /**
     * Set the cache with a key and value
     * @param string $key The unique key to store the value against
     * @param mixed $value The value of the MYSQL query 
     */
    public function setCache($key, $value){
        if($this->cacheEnabled){
            $this->cacheObj->save($key, $value);
        }
    }
    
    /**
     * Get the results for a given key
     * @param string $key The unique key to check for stored variables
     * @return mixed Returned the cached results from
     */
    public function getCache($key){
        if($this->modified === true || !$this->cacheEnabled){return false;}
        else{
            $this->cacheValue = $this->cacheObj->fetch($key);
            return $this->cacheValue;
        }
    }
    
    /**
     * Clears the cache
     */
    public function flushDB(){
        $this->cacheObj->deleteAll();
    }
}
