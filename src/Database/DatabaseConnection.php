<?php

namespace App\Database;

use PDO;
use PDOException;
use App\Config\Config;

class DatabaseConnection
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dbPath = Config::get('DB_PATH');
                self::$connection = new PDO('sqlite:' . $dbPath);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Handle exception
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$connection;
    }

    /**
     * Set a custom PDO connection for testing or other purposes.
     */
    public static function setConnection(PDO $pdo): void
    {
        self::$connection = $pdo;
    }
}