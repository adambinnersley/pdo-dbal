<?php
namespace DBAL\Caching;

interface CacheInterface{
    public function connect($host, $port);
    public function save($key, $value, $time = 0);
    public function replace($key, $value, $time = 0);
    public function fetch($key);
    public function delete($key);
    public function deleteAll();
}
