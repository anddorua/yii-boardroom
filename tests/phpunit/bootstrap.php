<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 14.10.15
 * Time: 23:52
 */
//include_once('AutoLoader.php');
// Register the directory to your include files
//AutoLoader::registerDirectory('classes/');
include_once('SplClassLoader.php');
$classLoader = new SplClassLoader('./');
$classLoader->register();
$classLoader = new SplClassLoader('tests/phpunit');
$classLoader->register();

define('TEST_DB_HOST', 'localhost'); // mysql server host
define('TEST_DB_NAME', 'bdr_test'); // database name
define('TEST_DB_USER', 'root'); // database user
define('TEST_DB_PASSWORD', ''); // user password
