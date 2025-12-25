<?php

namespace Connection;

class Connection
{
    private static $servername = 'mysql'; //container name
    private static $username = 'root';
    private static $password = 'ouichouani';
    private static $databasename = 'youcode_brief_9';

    public static function connect()
    {
        $connection = new \mysqli(self::$servername, self::$username, self::$password, self::$databasename);
        if ($connection->connect_error) die('connection fail : ' . $connection->connect_error);
        return $connection;
    }
}

