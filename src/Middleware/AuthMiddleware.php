<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Models\User;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // Extract the token from the request headers
        $token = $request->getHeaderLine('X-User-Token');

        if (empty($token)) {
            // Token is missing
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'Authentication token required']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Validate the token
        $user = User::findByToken($token);

        if (!$user) {
            // Invalid token
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'Invalid authentication token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Attach the user to the request
        $request = $request->withAttribute('user', $user);

        // Pass control to the next middleware or route handler
        return $handler->handle($request);
    }
}