<?php

namespace Tests\Controllers;

use Tests\ControllerTestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class MessageControllerTest extends ControllerTestCase
{
    public function testSendMessage()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $request = $this->createRequest('POST', "/groups/{$group->getId()}/messages", $user->getToken(), ['message' => 'Hello, world!']);
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($responseData['id']);
        $this->assertEquals('Hello, world!', $responseData['message']);
    }

    public function testListMessages()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        // Send a message
        $request = $this->createRequest('POST', "/groups/{$group->getId()}/messages", $user->getToken(), ['message' => 'Hello, world!']);
        $this->handleRequest($request);

        // List messages
        $request = $this->createRequest('GET', "/groups/{$group->getId()}/messages", $user->getToken());
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());

        $messages = json_decode((string) $response->getBody(), true);

        $this->assertCount(1, $messages);
        $this->assertEquals('Hello, world!', $messages[0]['message']);
    }
}