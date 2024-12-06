<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Database\DatabaseConnection;
use App\Helpers\TokenGenerator;

class User
{
    private int $id;
    private string $username;
    private string $token;

    private function __construct(int $id, string $username, string $token)
    {
        $this->id = $id;
        $this->username = $username;
        $this->token = $token;
    }

    public static function create(string $username, ?string $forcedToken = null): User
    {
        $pdo = DatabaseConnection::getConnection();
        $token = $forcedToken ?? TokenGenerator::generateToken();

        // Check if username already exists if you prefer a pre-check (optional)
        $checkUserStmt = $pdo->prepare("SELECT 1 FROM Users WHERE username = :username");
        $checkUserStmt->execute([':username' => $username]);
        if ($checkUserStmt->fetch()) {
            throw new \Exception("Username already taken");
        }

        // Pre-check if token already exists
        $checkStmt = $pdo->prepare("SELECT 1 FROM Users WHERE token = :token");
        $checkStmt->execute([':token' => $token]);
        if ($checkStmt->fetch()) {
            throw new \Exception("Token already taken");
        }

        $sql = "INSERT INTO Users (username, token) VALUES (:username, :token)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':token', $token);

        try {
            $stmt->execute();
            $id = $pdo->lastInsertId();
            return new User($id, $username, $token);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new \Exception("Token already taken");
            }
            throw new PDOException("Failed to create user: " . $e->getMessage());
        }
    }

    public static function findByToken(string $token): ?User
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM Users WHERE token = :token";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':token', $token);

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return new User($user['id'], $user['username'], $user['token']);
        }

        return null;
    }

    // Getter methods
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getToken(): string
    {
        return $this->token;
    }
}