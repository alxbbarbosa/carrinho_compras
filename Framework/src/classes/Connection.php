<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

final class Connection
{
    protected static $connection;
    protected static $user;
    protected static $passwd;
    public static $database;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __serialize()
    {
    }

    private static function connect($database, $host)
    {
        self::$database = $database;
        $pdo = new \PDO("pgsql:host={$host};dbname={$database}", self::$user, self::$passwd);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$connection = $pdo;
    }

    public static function setCredentials($user, $passwd)
    {
        self::$user = $user;
        self::$passwd = $passwd;
    }

    public static function getInstance($database, $host = 'localhost'): \PDO
    {
        if (empty(self::$connection)) {
            self::connect($database, $host);
        }
        return self::$connection;
    }
}
