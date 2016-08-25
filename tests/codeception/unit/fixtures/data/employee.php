<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 11.07.16
 * Time: 23:48
 */
Codeception\Util\Debug::debug("__employee fixture code__");
return [
    'emp1' => [
        'login' => 'admin',
        'email' => 'admin@gmail.com',
        'pwd_hash' => null,
        'is_admin' => 1,
        'hour_mode' => 24,
        'first_day' => 1,
        'name' => 'Andriy Doroshenko',
        'auth_key' => null,
    ],
    'emp2' => [
        'login' => 'user',
        'email' => 'user@gmail.com',
        'pwd_hash' => null,
        'is_admin' => 0,
        'hour_mode' => 12,
        'first_day' => 0,
        'name' => 'Ostap Bender',
        'auth_key' => null,
    ],
];