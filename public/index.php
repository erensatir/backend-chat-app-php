<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

$app->setBasePath('');

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// Custom Not Found Handler
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write('404 Not Found');
        return $response->withStatus(404);
    }
);
// Included routes
(require __DIR__ . '/../src/Routes/routes.php')($app);
// Run the application
$app->run();