<?php

require_once '../config.php';

class DatabaseConnection
{
    private static $pdo = null;

    private function __construct() {}

    private function __clone() {}

    public static function connect()
    {
        if (self::$pdo === null) {
            try {
                $db_host = DB_HOST;
                $db_name = DB_NAME;
                $db_username = DB_USERNAME;
                $db_password = DB_PASSWORD;
                $port = PORT;

                self::$pdo = new PDO("mysql:host=$db_host;dbname=$db_name;port=3345", $db_username, $db_password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

//    public static function connect()
//    {
//        $db_host = DB_HOST;
//        $db_name = DB_NAME;
//        $db_username = DB_USERNAME;
//        $db_password = DB_PASSWORD;
//        //database_connection.php
//        $pdo = new PDO("mysql:host=$db_host; dbname=$db_name", "$db_username", "$db_password");
//
//        return $pdo;
//    }
}
