<?php

namespace Tests\Controllers;

use Tests\ControllerTestCase;
use App\Models\User;

class UserControllerTest extends ControllerTestCase
{
    /**
     * Test successful user creation.
     */
    public function testCreateUserSuccess()
    {
        $payload = ['username' => 'testuser'];

        $request = $this->createRequest('POST', '/users', null, $payload);
        $response = $this->handleRequest($request);

        // Assert status code
        $this->assertEquals(201, $response->getStatusCode(), 'Expected status code 201 Created');

        // Assert response body
        $responseBody = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('id', $responseBody, 'Response should contain the user ID');
        $this->assertArrayHasKey('username', $responseBody, 'Response should contain the username');
        $this->assertArrayHasKey('token', $responseBody, 'Response should contain the token');
        $this->assertEquals('testuser', $responseBody['username'], 'Username should match the input');
    }

    /**
     * Test user creation without username.
     */
    public function testCreateUserWithoutUsername()
    {
        $payload = []; // Missing username

        $request = $this->createRequest('POST', '/users', null, $payload);
        $response = $this->handleRequest($request);

        // Assert status code
        $this->assertEquals(400, $response->getStatusCode(), 'Expected status code 400 Bad Request');

        // Assert response body
        $responseBody = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('error', $responseBody, 'Response should contain an error message');
        $this->assertEquals('Username is required', $responseBody['error'], 'Error message should indicate missing username');
    }

    /**
     * Test user creation with duplicate username.
     */
    public function testCreateUserDuplicateUsername()
    {
        $payload = ['username' => 'duplicateUser'];

        // First user creation
        $request1 = $this->createRequest('POST', '/users', null, $payload);
        $response1 = $this->handleRequest($request1);

        $this->assertEquals(201, $response1->getStatusCode(), 'Expected status code 201 Created for first user');

        // Second user creation with same username
        $request2 = $this->createRequest('POST', '/users', null, $payload);
        $response2 = $this->handleRequest($request2);

        // Assert status code
        $this->assertEquals(400, $response2->getStatusCode(), 'Expected status code 400 Bad Request for duplicate user');

        // Assert response body
        $responseBody = json_decode((string) $response2->getBody(), true);
        $this->assertArrayHasKey('error', $responseBody, 'Response should contain an error message');
        $this->assertEquals('Username already taken', $responseBody['error'], 'Error message should indicate duplicate username');
    }

    /**
     * Test user creation with empty username.
     */
    public function testCreateUserWithEmptyUsername()
    {
        $payload = ['username' => ''];

        $request = $this->createRequest('POST', '/users', null, $payload);
        $response = $this->handleRequest($request);

        // Assert status code
        $this->assertEquals(400, $response->getStatusCode(), 'Expected status code 400 Bad Request for empty username');

        // Assert response body
        $responseBody = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('error', $responseBody, 'Response should contain an error message');
        $this->assertEquals('Username is required', $responseBody['error'], 'Error message should indicate empty username');
    }

    /**
     * Test user creation with no request body.
     */
    public function testCreateUserWithoutRequestBody()
    {
        $request = $this->createRequest('POST', '/users');
        $response = $this->handleRequest($request);

        // Assert status code
        $this->assertEquals(400, $response->getStatusCode(), 'Expected status code 400 Bad Request for missing body');

        // Assert response body
        $responseBody = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('error', $responseBody, 'Response should contain an error message');
        $this->assertEquals('Username is required', $responseBody['error'], 'Error message should indicate missing request body');
    }
}