<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Message;
use App\Models\GroupMember;

class MessageController
{
    // Send a message to a group
    public function sendMessage(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $groupId = (int) $args['id'];
        $data = $request->getParsedBody();
        $messageText = $data['message'] ?? '';

        if (empty($messageText)) {
            $response->getBody()->write(json_encode(['error' => 'Message text is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            // Check if user is a member of the group
            if (!GroupMember::isUserInGroup($user->getId(), $groupId)) {
                throw new \Exception('You are not a member of this group.');
            }

            // Create the message
            $message = Message::create($groupId, $user->getId(), $messageText);

            // Respond with the created message
            $response->getBody()->write(json_encode([
                'id' => $message->getId(),
                'group_id' => $message->getGroupId(),
                'user_id' => $message->getUserId(),
                'message' => $message->getMessage(),
                'timestamp' => $message->getTimestamp(),
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    // List messages from a group
    public function listMessages(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $groupId = (int) $args['id'];
        $params = $request->getQueryParams();
        $since = $params['since'] ?? null;

        try {
            // Check if user is a member of the group
            if (!GroupMember::isUserInGroup($user->getId(), $groupId)) {
                throw new \Exception('You are not a member of this group.');
            }

            // Retrieve messages
            $messages = Message::getMessagesByGroup($groupId, $since);
            $messagesArray = array_map(function ($message) {
                return [
                    'id' => $message->getId(),
                    'user_id' => $message->getUserId(),
                    'message' => $message->getMessage(),
                    'timestamp' => $message->getTimestamp(),
                ];
            }, $messages);

            $response->getBody()->write(json_encode($messagesArray));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}