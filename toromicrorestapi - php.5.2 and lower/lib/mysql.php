<?php

class MySQL {

    private static $instance = NULL;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance() {
        if (!self::$instance) {
            throw new Exception("First create instanse, use Mysql::Create (param..)");
        }
        return self::$instance;
    }

    public static function create($host, $dbname, $user, $password) {
        if (!self::$instance) {
            try {
               
                
                self::$instance = new PDO("mysql:host=$host;dbname=$dbname", "$user", "$password");
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->exec("set names utf8");
               // echo 'Connected to database';
                
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return self::$instance;
    }

}
