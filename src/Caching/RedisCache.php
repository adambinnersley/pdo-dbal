<?php
namespace DBAL\Caching;

class RedisCache implements CacheInterface{
    
    protected $redisCache;

    /**
     * constructor
     */
    public function __construct(){
        if(!extension_loaded('redis')) {
            throw new Exception('Redis extension is not loaded');
        }
        $this->redisCache = new Redis();
    }
    
    /**
     * destructor closes the connection
     */
    public function __destruct(){
        if (is_object($this->redisCache)) {
            $this->redisCache->close();
        }
    }

    /**
     * Connect to a server
     * @param string $host This should be the host name or IP address you want to connect to
     * @param int $port The port number where Memcache can be accessed
     * @param boolean $persistent If you want this connection to be persistent set to true else set to false
     * @return $this
     */
    public function connect($host, $port, $persistent = false){
        $this->addServer($host, $port, $persistent);
        return $this;
    }
    
    /**
     * Add a server to connection pool
     * @param string $host This should be the host name or IP address you want to add to the Memcache pool
     * @param int $port The port number where Memcache can be accessed
     * @param boolean $persistent If you want this connection to be persistent set to true else set to false
     * @return $this
     */
    public function addServer($host, $port, $persistent = false){
        if ($persistent === false) {
            $this->redisCache->connect($host, intval($port));
        }
        else {
            $this->redisCache->pconnect($host, intval($port));
        }
        return $this;
    }
    

    /**
     * Adds a value to be stored on the server
     * @param string $key This should be the key for the value you wish to add
     * @param mixed $value The value you wish to be stored with the given key
     * @param int $time How long should the value be stored for in seconds (0 = never expire) (max set value = 2592000 (30 Days))
     * @return boolean Returns true if successfully added or false on failure
     */
    public function save($key, $value, $time = 0){
        return $this->redisCache->set($key, $value, intval($time));
    }
    
    
    /**
     * Replaces a stored value for a given key 
     * @param string $key This should be the key for the value you wish to replace
     * @param mixed $value The new value that you wish to give to that key
     * @param int $time How long should the value be stored for in seconds (0 = never expire) (max set value = 2592000 (30 Days))
     * @return boolean Returns true if successfully replaced or false on failure
     */
    public function replace($key, $value, $time = 0){
        return $this->save($key, $value, $time);
    }
    
    /**
     * Returns the values store for the given key
     * @param string $key This should be the unique query key to get the value
     * @return mixed The store value will be returned
     */
    public function fetch($key){
        return $this->redisCache->get($key);
    }
    
    /**
     * Deletes a single value from the server based on the given key
     * @param string $key This should be the key that you wish to delete the value for
     * @return boolean Returns true on success or false on failure
     */
    public function delete($key){
        return $this->redisCache->delete($key);
    }
    
    /**
     * Deletes all values from the server
     * @return boolean Returns true on success or false on failure
     */
    public function deleteAll(){
        return $this->redisCache->flushAll();
    }
}
