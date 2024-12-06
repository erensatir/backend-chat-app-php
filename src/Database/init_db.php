<?php

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/DatabaseConnection.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\DatabaseConnection;

try {
    $pdo = DatabaseConnection::getConnection();

    // Check if tables exist before creating
    $checkTableSQL = "SELECT name FROM sqlite_master WHERE type='table' AND name=:table";
    $tables = ['Users', 'Groups', 'GroupMembers', 'Messages'];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare($checkTableSQL);
        $stmt->execute([':table' => $table]);

        if (!$stmt->fetch()) {
            // Create tables only if they do not exist
            switch ($table) {
                case 'Users':
                    $pdo->exec("
                        CREATE TABLE Users (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            username TEXT UNIQUE NOT NULL,
                            token TEXT UNIQUE NOT NULL
                        );
                    ");
                    break;
                case 'Groups':
                    $pdo->exec("
                        CREATE TABLE Groups (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            name TEXT UNIQUE NOT NULL
                        );
                    ");
                    break;
                case 'GroupMembers':
                    $pdo->exec("
                        CREATE TABLE GroupMembers (
                            user_id INTEGER NOT NULL,
                            group_id INTEGER NOT NULL,
                            PRIMARY KEY (user_id, group_id),
                            FOREIGN KEY (user_id) REFERENCES Users(id),
                            FOREIGN KEY (group_id) REFERENCES Groups(id)
                        );
                    ");
                    break;
                case 'Messages':
                    $pdo->exec("
                        CREATE TABLE Messages (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            group_id INTEGER NOT NULL,
                            user_id INTEGER NOT NULL,
                            message TEXT NOT NULL,
                            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (group_id) REFERENCES Groups(id),
                            FOREIGN KEY (user_id) REFERENCES Users(id)
                        );
                    ");
                    break;
            }
        }
    }

} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}