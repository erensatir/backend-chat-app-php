<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static $instance = null;

    private function __construct()
    {
        // Load the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public static function get($key, $default = null)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        $value = $_ENV[$key] ?? $default;

        // Resolve DB_PATH to absolute path
        if ($key === 'DB_PATH' && $value) {
            $baseDir = getenv('APP_BASE_DIR') ?: dirname(__DIR__, 2);
            if (!str_starts_with($value, '/')) {
                $value = $baseDir . DIRECTORY_SEPARATOR . $value;
            }
        }

        return $value;
    }
}