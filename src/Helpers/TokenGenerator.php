<?php

namespace App\Helpers;

class TokenGenerator
{
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}