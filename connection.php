<?php

class Database{

    public static $connection;

    public static function setUpConnection(){
        if(!isset(Database::$connection)){
            Database::$connection = new mysqli("localhost","root","","nsbmmarket","3306");
        }
    }

    public static function iud($q){
        Database::setUpConnection();
        return Database::$connection->query($q);
    }

    public static function search($q){
        Database::setUpConnection();
        $resultset = Database::$connection->query($q);
        return $resultset;
    }

    // Escapes a value so it is safe to place inside a query string.
    public static function escape($value){
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    // Returns the auto-increment id created by the most recent INSERT.
    public static function insertId(){
        Database::setUpConnection();
        return Database::$connection->insert_id;
    }

}

?>
