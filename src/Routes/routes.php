<?php

use Slim\App;
use App\Controllers\GroupController;
use App\Controllers\MessageController;

return function (App $app) {
    // Group Routes
    $app->post('/groups', [GroupController::class, 'createGroup']);
    $app->post('/groups/{id}/join', [GroupController::class, 'joinGroup']);
    $app->get('/groups', [GroupController::class, 'listGroups']);

    // Message Routes
    $app->post('/groups/{id}/messages', [MessageController::class, 'sendMessage']);
    $app->get('/groups/{id}/messages', [MessageController::class, 'listMessages']);
};