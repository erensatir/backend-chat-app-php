<?php

namespace App\Database;

use PDO;
use PDOException;
use App\Config\Config;

class DatabaseConnection
{
    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            try {
                $dbPath = Config::get('DB_PATH');
                self::$connection = new PDO('sqlite:' . $dbPath);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Handle exception (e.g., log the error)
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$connection;
    }
}