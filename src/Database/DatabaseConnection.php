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
            // Initialize the database schema before establishing the main connection
            require_once __DIR__ . '/init_db.php';

            try {
                $dbPath = Config::get('DB_PATH');
                self::$connection = new PDO('sqlite:' . $dbPath);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$connection;
    }

    /**
     * Allow setting a custom PDO connection if needed (e.g., in tests)
     */
    public static function setConnection(PDO $pdo): void
    {
        self::$connection = $pdo;
    }
}