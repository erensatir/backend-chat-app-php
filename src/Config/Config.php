<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static $instance = null;

    private function __construct()
    {
        // Load the .env file
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
    }

    public static function get($key, $default = null)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // If the value is a relative path, resolve it to an absolute path
        $value = $_ENV[$key] ?? $default;
        if ($key === 'DB_PATH' && $value && !str_starts_with($value, '/')) {
            return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $value;
        }

        return $value;
    }
}