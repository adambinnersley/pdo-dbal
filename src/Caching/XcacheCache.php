<?php
namespace DBAL\Caching;

use Exception;

/**
 * @codeCoverageIgnore
 */
class XcacheCache implements CacheInterface{
    
    protected $cache;

    /**
     * constructor
     */
    public function __construct(){
        if (!extension_loaded('xcache')) {
            throw new Exception('xCache extension is not loaded');
        }
    }

    /**
     * Connect to a server
     * @param string $host This should be the host name or IP address you want to connect to
     * @param int $port The port number where Memcache can be accessed
     * @return $this
     */
    public function connect($host, $port){
        $this->cache = new \Xcache();
    }
    

    /**
     * Adds a value to be stored on the server
     * @param string $key This should be the key for the value you wish to add
     * @param mixed $value The value you wish to be stored with the given key
     * @param int $time How long should the value be stored for in seconds (0 = never expire) (max set value = 2592000 (30 Days))
     * @return boolean Returns true if successfully added or false on failure
     */
    public function save($key, $value, $time = 0){
        return xcache_set($key, $value, intval($time));
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
        return xcache_get($key); 
    }
    
    /**
     * Deletes a single value from the server based on the given key
     * @param string $key This should be the key that you wish to delete the value for
     * @return boolean Returns true on success or false on failure
     */
    public function delete($key){
        return xcache_unset($key);
    }
    
    /**
     * Deletes all values from the server
     * @return boolean Returns true on success or false on failure
     */
    public function deleteAll(){
        return xcache_clear_cache(XC_TYPE_VAR, 0);
    }
}
