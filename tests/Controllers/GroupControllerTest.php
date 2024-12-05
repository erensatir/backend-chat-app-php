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
}