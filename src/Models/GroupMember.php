<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Database\DatabaseConnection;

class GroupMember
{
    public static function addUserToGroup(int $userId, int $groupId): bool
    {
        $pdo = DatabaseConnection::getConnection();

        // Check if user exists
        $userCheck = $pdo->prepare("SELECT 1 FROM Users WHERE id = :uid");
        $userCheck->execute([':uid' => $userId]);
        if (!$userCheck->fetch()) {
            return false;
        }

        // Check if group exists
        $groupCheck = $pdo->prepare("SELECT 1 FROM Groups WHERE id = :gid");
        $groupCheck->execute([':gid' => $groupId]);
        if (!$groupCheck->fetch()) {
            return false;
        }

        // Check if user is already a member
        if (self::isUserInGroup($userId, $groupId)) {
            throw new \Exception("User is already a member of the group.");
        }

        $stmt = $pdo->prepare("INSERT INTO GroupMembers (user_id, group_id) VALUES (:user_id, :group_id)");
        try {
            $stmt->execute([':user_id' => $userId, ':group_id' => $groupId]);
            return true;
        } catch (\PDOException $e) {
            return false;
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