<?php

namespace Tests\Controllers;

use Tests\ControllerTestCase;
use App\Models\User;

class GroupControllerTest extends ControllerTestCase
{
    public function testCreateGroup()
    {
        $user = User::create('testuser');

        $request = $this->createRequest('POST', '/groups', $user->getToken(), ['name' => 'Test Group']);
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($responseData['id']);
        $this->assertEquals('Test Group', $responseData['name']);
    }

    public function testListGroups()
    {
        $user = User::create('testuser');

        // Create a group
        $request = $this->createRequest('POST', '/groups', $user->getToken(), ['name' => 'Test Group']);
        $this->handleRequest($request);

        // List groups
        $request = $this->createRequest('GET', '/groups', $user->getToken());
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());

        $groups = json_decode((string) $response->getBody(), true);

        $this->assertCount(1, $groups);
        $this->assertEquals('Test Group', $groups[0]['name']);
    }

    
    // Attempt to create a group without a name field.
    // Expected result: 400 Bad Request.
    public function testCreateGroupWithoutNameField()
    {
        $user = User::create('controllerUser');
        $request = $this->createRequest('POST', '/groups', $user->getToken(), []); // No 'name' key
        $response = $this->handleRequest($request);

        $this->assertEquals(400, $response->getStatusCode(), 'Expected 400 when creating group without a name.');
    }

    
    // Attempt to create a group with an empty or invalid name.
    // Expected result: 400 Bad Request.
    public function testCreateGroupWithEmptyName()
    {
        $user = User::create('controllerUser2');
        $request = $this->createRequest('POST', '/groups', $user->getToken(), ['name' => '']);
        $response = $this->handleRequest($request);

        $this->assertEquals(400, $response->getStatusCode(), 'Expected 400 when creating group with empty name.');
    }

    
    // Attempt to list groups when none exist.
    // Expected result: empty array returned with 200 OK.
    public function testListGroupsWhenNoneExist()
    {
        $user = User::create('controllerUser3');
        $request = $this->createRequest('GET', '/groups', $user->getToken());
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $groups = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($groups, 'Expected an array');
        $this->assertCount(0, $groups, 'Expected no groups when none are created.');
    }

    
    // Attempt to hit the endpoint without a valid X-User-Token.
    // Expected result: 401 Unauthorized.
    public function testListGroupsWithoutToken()
    {
        // No token header
        $request = $this->createRequest('GET', '/groups', null);
        $response = $this->handleRequest($request);

        $this->assertEquals(401, $response->getStatusCode(), 'Expected 401 when no token is provided.');
    }
}