<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Database\DatabaseConnection;

class GroupMember
{
    public static function addUserToGroup(int $userId, int $groupId): void
    {
        $pdo = DatabaseConnection::getConnection();

        // Check if already a member
        if (self::isUserInGroup($userId, $groupId)) {
            throw new \Exception("User is already a member of the group.");
        }

        $sql = "INSERT INTO GroupMembers (user_id, group_id) VALUES (:user_id, :group_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':group_id', $groupId);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Failed to add user to group: " . $e->getMessage());
        }
    }

    public static function isUserInGroup(int $userId, int $groupId): bool
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT 1 FROM GroupMembers WHERE user_id = :user_id AND group_id = :group_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':group_id', $groupId);

        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public static function getUserGroups(int $userId): array
    {
        $pdo = DatabaseConnection::getConnection();
        $sql = "SELECT g.* FROM Groups g
                INNER JOIN GroupMembers gm ON g.id = gm.group_id
                WHERE gm.user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);

        $stmt->execute();
        $groups = [];
        while ($group = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = new Group($group['id'], $group['name']);
        }

        return $groups;
    }
}