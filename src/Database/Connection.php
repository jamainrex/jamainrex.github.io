<?php
namespace DirectoryCrawler\Database;

use DirectoryCrawler\Config\Config;
Class Connection {
    protected $model;
    static $connection;
    static $config;

    public function __construct(){
        self::$config = Config::$config;
        if (empty(self::$connection)) {
            $this->dbconnect();
        }
    }
    
    public function dbconnect(){
        self::$connection = mysqli_connect(self::$config['host'], self::$config['user'], self::$config['pass'], self::$config['db']);
        if (empty(self::$connection)) {
            die();
        }
        return self::$connection;
    }

    public function getExistingConnection()
    {
        if (!empty(self::$connection)) {
            return self::$connection;
        } else {
            return false;
        }
    }

}
