<?php

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/DatabaseConnection.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\DatabaseConnection;

try {
    $pdo = DatabaseConnection::getConnection();

    // Users Table
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            token TEXT UNIQUE NOT NULL
        );
    ";
    // Groups Table
    $createGroupsTable = "
        CREATE TABLE IF NOT EXISTS Groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL
        );
    ";
    // Group Members Table (Many-to-Many)
    $createGroupMembersTable = "
        CREATE TABLE IF NOT EXISTS GroupMembers (
            user_id INTEGER NOT NULL,
            group_id INTEGER NOT NULL,
            PRIMARY KEY (user_id, group_id),
            FOREIGN KEY (user_id) REFERENCES Users(id),
            FOREIGN KEY (group_id) REFERENCES Groups(id)
        );
    ";
    // Messages Table (One-to-Many)
    $createMessagesTable = "
        CREATE TABLE IF NOT EXISTS Messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            message TEXT NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES Groups(id),
            FOREIGN KEY (user_id) REFERENCES Users(id)
        );
    ";

    // Execute SQL statements
    $pdo->exec($createUsersTable);
    $pdo->exec($createGroupsTable);
    $pdo->exec($createGroupMembersTable);
    $pdo->exec($createMessagesTable);

} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}