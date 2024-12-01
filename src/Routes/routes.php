<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\GroupController;
use App\Controllers\MessageController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    // Protected routes (require authentication)
    $app->group('', function (RouteCollectorProxy $group) {
        // Group Routes
        $group->post('/groups', [GroupController::class, 'createGroup']);
        $group->post('/groups/{id}/join', [GroupController::class, 'joinGroup']);
        $group->get('/groups', [GroupController::class, 'listGroups']);

        // Message Routes
        $group->post('/groups/{id}/messages', [MessageController::class, 'sendMessage']);
        $group->get('/groups/{id}/messages', [MessageController::class, 'listMessages']);
    })->add(new AuthMiddleware());
};