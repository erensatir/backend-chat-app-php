<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Database\DatabaseConnection;

class Message
{
    private int $id;
    private int $groupId;
    private int $userId;
    private string $message;
    private string $timestamp;

    private function __construct(int $id, int $groupId, int $userId, string $message, string $timestamp)
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->message = $message;
        $this->timestamp = $timestamp;
    }

    public static function create(int $groupId, int $userId, string $message): Message
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "INSERT INTO Messages (group_id, user_id, message) VALUES (:group_id, :user_id, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);

        try {
            $stmt->execute();
            $id = $pdo->lastInsertId();
            $timestamp = date('Y-m-d H:i:s');
            return new Message($id, $groupId, $userId, $message, $timestamp);
        } catch (PDOException $e) {
            throw new PDOException("Failed to create message: " . $e->getMessage());
        }
    }

    public static function getMessagesByGroup(int $groupId, ?string $since = null): array
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM Messages WHERE group_id = :group_id";
        if ($since) {
            $sql .= " AND timestamp > :since";
        }
        $sql .= " ORDER BY timestamp ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':group_id', $groupId);
        if ($since) {
            $stmt->bindParam(':since', $since);
        }

        $stmt->execute();
        $messages = [];
        while ($message = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message(
                $message['id'],
                $message['group_id'],
                $message['user_id'],
                $message['message'],
                $message['timestamp']
            );
        }

        return $messages;
    }

    // Getter methods
    public function getId(): int { return $this->id; }
    public function getGroupId(): int { return $this->groupId; }
    public function getUserId(): int { return $this->userId; }
    public function getMessage(): string { return $this->message; }
    public function getTimestamp(): string { return $this->timestamp; }
}