<?php
namespace DBAL;

interface DBInterface{
    public function query($sql);
    public function select($table);
    public function selectAll($table);
    public function fetchColumn($table);
    public function insert($table, $records);
    public function update($table, $records);
    public function delete($table, $where);
    public function count($table);
    public function truncate($table);
    public function numRows();
    public function rowCount();
    public function lastInsertId();
}
