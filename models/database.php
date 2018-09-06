<?php

require_once 'config/database_config.php';
require_once 'models/database/dbagent.class.php';
require_once 'models/database/postgres.class.php';

class Database {
    
    private static $_currentDb;
    /**
     * share instance database
     */
     public static function currentDb(){
        if (self::$_currentDb == null){
            global $database_config;
            self::$_currentDb = self::openDb($database_config);
        }

        return self::$_currentDb;
    }

    /**
     * open database with config
     * @param  [array] $config
     * @return [PostgresDB] after open
     */
    private static function openDb($config){
        $db = new PostgresDb();
        $db->open($config["host"],$config["port"],$config["name"],$config["user"],$config["pass"]);
        return $db;
    }
}

?>