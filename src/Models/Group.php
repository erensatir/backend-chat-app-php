<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Database\DatabaseConnection;

class Group
{
    private int $id;
    private string $name;

    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(string $name): Group
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "INSERT INTO Groups (name) VALUES (:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);

        try {
            $stmt->execute();
            $id = $pdo->lastInsertId();
            return new Group($id, $name);
        } catch (PDOException $e) {
            throw new PDOException("Failed to create group: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?Group
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM Groups WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($group) {
            return new Group($group['id'], $group['name']);
        }

        return null;
    }

    public static function findByName(string $name): ?Group
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM Groups WHERE name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);

        $stmt->execute();
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($group) {
            return new Group($group['id'], $group['name']);
        }

        return null;
    }

    public static function getAllGroups(): array
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM Groups";
        $stmt = $pdo->query($sql);

        $groups = [];
        while ($group = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = new Group($group['id'], $group['name']);
        }

        return $groups;
    }

    // Getter methods
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}