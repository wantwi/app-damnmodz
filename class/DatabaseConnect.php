<?php
class DatabaseConnection
{
    public static function connect()
    {
        $db_host = DB_HOST;
        $db_name = DB_NAME;
        $db_username = DB_USERNAME;
        $db_password = DB_PASSWORD;
        $db_port = PORT;
        //database_connection.php
        $pdo = new PDO("mysql:host=$db_host;port=$db_port; dbname=$db_name", "$db_username", "$db_password");

        return $pdo;
    }
}
