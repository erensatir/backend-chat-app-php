<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;

$username = 'testuser2';
$user = User::create($username);
echo "User created:\n";
echo "Username: {$user->getUsername()}\n";
echo "Token: {$user->getToken()}\n";