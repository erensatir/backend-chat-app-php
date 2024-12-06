<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function createUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';

        if (empty($username)) {
            $response->getBody()->write(json_encode(['error' => 'Username is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            // Create the user using the User model
            $user = User::create($username);
            $response->getBody()->write(json_encode([
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'token' => $user->getToken(),
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // If a duplicate username or token error occurs, we return an appropriate response
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}