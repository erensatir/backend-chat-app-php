<?php

namespace Tests;

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class ControllerTestCase extends BaseTestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = AppFactory::create();

        $this->app = AppFactory::create();
        $this->app->addBodyParsingMiddleware();
        (require __DIR__ . '/../src/Routes/routes.php')($this->app);
    }

    protected function createRequest($method, $uri, $token = null, $data = null)
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest($method, $uri);

        if ($token) {
            $request = $request->withHeader('X-User-Token', $token);
        }

        if ($data) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $request->getBody()->write(json_encode($data));
            $request->getBody()->rewind();
        }

        return $request;
    }

    protected function handleRequest($request)
    {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        return $this->app->handle($request);
    }
}