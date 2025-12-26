<?php

namespace Connection;
class Connection
{
    private static $servername = 'mysql'; //container name
    private static $username = 'root';
    private static $password = 'ouichouani';
    private static $databasename = 'youcode_brief_9';

    public static function connect_mysqli()
    {
        $connection = new \mysqli(self::$servername, self::$username, self::$password, self::$databasename);
        if ($connection->connect_error) die('connection fail : ' . $connection->connect_error);
        return $connection;
    }

    public static function connect_PDO()
    {
        $option = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,

        ];
        $connection = new \PDO("mysql:host=". self::$servername.";dbname=" . self::$databasename, self::$username, self::$password, $option);
        return $connection;
    }
}

