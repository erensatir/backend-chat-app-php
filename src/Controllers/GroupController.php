<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;

class GroupController
{
    // Create a new group
    public function createGroup(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $data = $request->getParsedBody();
        $groupName = $data['name'] ?? '';

        if (empty($groupName)) {
            $response->getBody()->write(json_encode(['error' => 'Group name is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            // Create the group
            $group = Group::create($groupName);

            // Add the user as a member of the group
            GroupMember::addUserToGroup($user->getId(), $group->getId());

            // Respond with the created group
            $response->getBody()->write(json_encode([
                'id' => $group->getId(),
                'name' => $group->getName(),
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    // Join an existing group
    public function joinGroup(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $groupId = (int) $args['id'];

        try {
            // Check if the group exists
            $group = Group::findById($groupId);
            if (!$group) {
                throw new \Exception('Group not found.');
            }

            // Add the user to the group
            GroupMember::addUserToGroup($user->getId(), $groupId);

            $response->getBody()->write(json_encode(['message' => 'Joined group successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    // List all groups
    public function listGroups(Request $request, Response $response): Response
    {
        $groups = Group::getAllGroups();
        $groupsArray = array_map(function ($group) {
            return [
                'id' => $group->getId(),
                'name' => $group->getName(),
            ];
        }, $groups);

        $response->getBody()->write(json_encode($groupsArray));
        return $response->withHeader('Content-Type', 'application/json');
    }
}