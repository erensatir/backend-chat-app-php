<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Database\DatabaseConnection;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        // Initialize the database before each test
        $this->initializeDatabase();
    }

    protected function initializeDatabase()
    {
        // Set up an in-memory SQLite database
        DatabaseConnection::setConnection(new \PDO('sqlite::memory:'));

        // Run the database schema creation script
        $db = DatabaseConnection::getConnection();
        $db->exec("
            CREATE TABLE Users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                token TEXT NOT NULL UNIQUE
            );

            CREATE TABLE Groups (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
            );

            CREATE TABLE GroupMembers (
                user_id INTEGER NOT NULL,
                group_id INTEGER NOT NULL,
                PRIMARY KEY (user_id, group_id),
                FOREIGN KEY (user_id) REFERENCES Users(id),
                FOREIGN KEY (group_id) REFERENCES Groups(id)
            );

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
    }
}