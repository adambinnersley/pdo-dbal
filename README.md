[![Build Status](https://api.travis-ci.org/AdamB7586/pdo-dbal.png)](https://api.travis-ci.org/AdamB7586/pdo-dbal)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/AdamB7586/pdo-dbal/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AdamB7586/pdo-dbal/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg?style=flat-circle)](https://php.net/)
[![Scrutinizer Coverage](https://scrutinizer-ci.com/g/AdamB7586/pdo-dbal/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AdamB7586/pdo-dbal/)

# PDO Database Abstraction Layer
A simple database abstraction layer for MySQL PDO 

## Installation

Installation is available via [Composer/Packagist](https://packagist.org/packages/adamb/database), you can add the following line to your `composer.json` file:

```json
"adamb/database": "^1.0"
```

or

```sh
composer require adamb/database
```

## Class Features
- Optional cache support with Memcache / Memcached / Redis / XCache
- Optional connection to secondary database incase the no connection to the primary MySQL server is available
- Connects to a MySQL database via PDO
- Simplify queries to SELECT / INSERT / UPDATE and DELETE
- Built in prepared statements 
- Compatible with PHP5.6 and later

## License

This software is distributed under the [MIT](https://github.com/AdamB7586/pdo-dbal/blob/master/LICENSE) license. Please read LICENSE for information on the
software availability and distribution.


## Usage

Example of usage can be found below with what queries they would result in (For security all queries are run using prepared statements with values added on execute() after the prepare() has been run)

### 1. Connect
```php
<?php

$hostname = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'my_db';
$backup_server = '127.0.0.2';

// Connect to a single MySQL server
$db = new DBAL\Database($hostname, $username, $password, $database);

// Connect to MySQL server and is primary server is down connect to secondary server
$db = new DBAL\Database($hostname, $username, $password, $database, $backup_server);

```

### 2. Select
```php

$db->select('test_table');
// Query Run = "SELECT * FROM `test_table` LIMIT 1;"

$db->select('test_table', array('id' => 3));
// Query Run = "SELECT * FROM `test_table` WHERE `id` = 3 LIMIT 1;"

$db->select('test_table', array('id' => array('>=', 3)));
// Query Run = "SELECT * FROM `test_table` WHERE `id` >= 3 LIMIT 1;"

$db->select('test_table', array('id' => array('>=', 3)), array('name', 'email'));
// Query Run = "SELECT `name`, `email` FROM `test_table` WHERE `id` >= 3 LIMIT 1;"

$db->select('test_table', array('id' => array('>=', 3)), array('name', 'email'), array('id' => 'DESC'));
// Query Run = "SELECT `name`, `email` FROM `test_table` WHERE `id` >= 3 ORDER BY `id` DESC LIMIT 1;"

// Usage of IN or NOT IN operator
$db->select('test_table', array('id' => array('IN' => array(1, 2, 3))));
// Query Run = "SELECT * FROM `test_table` WHERE `id` IN (1,2,3) LIMIT 1;"

$db->select('test_table', array('id' => array('NOT IN' => array(2, 3))));
// Query Run = "SELECT * FROM `test_table` WHERE `id` NOT IN (2,3) LIMIT 1;"

// Usage of BETWEEN or NOT BETWEEN operator
$db->select('test_table', array('id' => array('BETWEEN' => array(1, 3))));
// Query Run = "SELECT * FROM `test_table` WHERE `id` BETWEEN 1 AND 3 LIMIT 1;"

$db->select('test_table', array('id' => array('NOT BETWEEN' => array(2, 10))));
// Query Run = "SELECT * FROM `test_table` WHERE `id` NOT BETWEEN 2 AND 10 LIMIT 1;"

// The same functions can be run using selectAll() rather than select()

$db->selectAll('test_table', array('id' => array('>=', 3)), array('name', 'email'), array('id' => 'DESC'), 150);
// Query Run = "SELECT `name`, `email` FROM `test_table` WHERE `id` >= 3 ORDER BY `id` DESC LIMIT 150;"

// Usage
// select($table, $where = array('field_name' => $value), $selectfield = array('field_1', 'field_2'), $order = array('field_name' => 'ASC' or 'DESC') or RAND());
// selectAll($table, $where = array('field_name' => $value), $selectfield = array('field_1', 'field_2'), $order = array('field_name' => 'ASC' or 'DESC') or RAND(), $limit(default = no limit));

```

### 3. Insert
```php

$db->insert('test_table', array('name' => 'Bob', 'email' => 'bob@gmail.com'));
// Query Run = "INSERT INTO `test_table` (`name`, `email`) VALUES ('Bob', 'bob@gmail.com');"

// Usage
// insert($table, array('field_name' => $value));

```

### 4. Update
```php

$db->update('test_table', array('name' => 'Ken', 'email' => 'ken@gmail.com'), array('id' => 12345));
// Query Run = "UPDATE `test_table` SET (`name` => 'Ken', `email` => 'ken@gmail.com') WHERE `id` = 12345;"

$db->update('test_table', array('name' => 'Ken'), array('name' => 'Bob'), 50);
// Query Run = "UPDATE `test_table` SET (`name` => 'Ken') WHERE `name` = 'Bob' LIMIT 50;"

// Usage
// update($table, $updatevalues = array('field_name' => $value), $where = array('field' => $value));

```

### 5. Delete
```php

$db->delete('test_table', array('id' => 1));
// Query Run = "DELETE FROM `test_table` WHERE `id` = 1;"

$db->delete('test_table', array('name' => 'Ted'), 50);
// Query Run = "DELETE FROM `test_table` WHERE `name` = 'Ted' LIMIT 50;"

// Usage
// delete($table, $where = array('field' => $value), $limit);

```

### 6. Count
```php

$db->count('test_table');
// Query Run = "SELECT COUNT(*) FROM `test_table`;";

$db->count('test_table', array('name' => 'Bob'));
// Query Run = "SELECT COUNT(*) FROM `test_table` WHERE `name` => 'Bob';";

// Usage
// count($table, $where = array('field' => $value));

```

### 7. Query
```php

Any query can be run using the query command by passing the SQL query and values

$db->query("SELECT * FROM `test_table` WHERE `name` = ? OR `name` => ?;", array('John', 'Phil'));
// Query Run = "SELECT * FROM `test_table` WHERE `name` = 'John' OR `name` => 'Phil';";

$db->query("UPDATE `test_table` SET `name` = 'Karl' WHERE `name` = ? OR `name` => ?;", array('John', 'Phil'));
// Query Run = "UPDATE `test_table` SET `name` = 'Karl' WHERE `name` = 'John' OR `name` => 'Phil';";

```

### 8. FetchColumn
```php

// This is similar to the select method except return the column value rather than an array of all of the fields 

$column = $db->fetchColumn('test_table', array('id' => 3), array('name', 'email'));
// Query Run = "SELECT `name`, `email` FROM `test_table` WHERE `id` = 3 LIMIT 1;"
echo($column[0]); // will return the name field
echo($column[1]); // will return the email field

$column = $db->fetchColumn('test_table', array('id' => 3), array('name', 'email'), 1);
echo($column); // will return email as the field number of 1 has been set

```

### 9. NumRows
```php

$db->numRows();
$db->rowCount();

// Running either of these methods after executing a query will show you how many rows have been affected e.g.
$db->update('test_table', array('name' => 'Ken'), array('name' => 'Bob'));
echo($db->numRows()); // Returns number of results updated e.g. 12

```

### 10. LastInsertId
```php

$db->insert('test_table', array('name' => 'Bob', 'email' => 'bob@gmail.com'));
echo($db->lastInsertId()); // Returns the key field value number normally the the auto increment field value

```

### 11. isConnected
```php

$db->isConnected(); // Returns true of false depending on if the connection is active to the server

```

### 12. Caching

Database caching can be added to queries with support for Memcache / Memcached / Redis and XCache

```php

$caching = new DBAL\Caching\Memcached();
$db = new DBAL\Database($hostname, $username, $password, $database, $backup_server, $caching);

// OR

$caching = new DBAL\Caching\Memcached();
$db = new DBAL\Database($hostname, $username, $password, $database);
$db->setCaching($caching);

```