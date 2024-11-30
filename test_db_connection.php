<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\DatabaseConnection;

try {
    $pdo = DatabaseConnection::getConnection();
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table';");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in the database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}