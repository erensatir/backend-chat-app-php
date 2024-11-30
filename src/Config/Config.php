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

        return $_ENV[$key] ?? $default;
    }
}