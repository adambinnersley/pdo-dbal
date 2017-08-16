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
- Optional cache support with APC / Memcache / Memcached / Redis / XCache
- Optional connection to secondary database incase the no connection to the primary MySQL server is available
- Connects to a MySQL database via PDO
- Simplify queries to SELECT/UPDATE and DELETE results
- Built in prepared statements 
- Compatible with PHP5.3 and later

## License

This software is distributed under the [MIT](https://github.com/AdamB7586/pdo-dbal/blob/master/LICENSE) license. Please read LICENSE for information on the
software availability and distribution.


## An Example
```php
<?php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'my_db';

$db = new DBAL\Database($hostname, $username, $password, $database);

```