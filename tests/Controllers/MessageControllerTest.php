<?php

namespace Tests\Controllers;

use Tests\ControllerTestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;

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

    // Sending a message without a message field or with empty message.
    // Expected result: 400 Bad Request.
    public function testSendMessageWithoutText()
    {
        $user = User::create('msgControllerUser');
        $group = Group::create('MsgControllerGroup');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $request = $this->createRequest('POST', "/groups/{$group->getId()}/messages", $user->getToken(), []);
        $response = $this->handleRequest($request);
        $this->assertEquals(400, $response->getStatusCode(), 'Expected 400 when message text is missing.');
    }

    // Trying to send a message to a group the user hasn’t joined.
    // Expected result: 403 Forbidden.
    public function testSendMessageNotInGroup()
    {
        $user = User::create('msgControllerUser2');
        $group = Group::create('MsgControllerGroup2');
        // User is NOT added to the group

        $request = $this->createRequest('POST', "/groups/{$group->getId()}/messages", $user->getToken(), ['message' => 'Hello']);
        $response = $this->handleRequest($request);
        $this->assertEquals(403, $response->getStatusCode(), 'Expected 403 when sending message to a group user is not in.');
    }


    // Listing messages from a group with no messages.
    // Expected result: empty array with 200 OK.
    public function testListMessagesFromEmptyGroup()
    {
        $user = User::create('msgControllerUser3');
        $group = Group::create('MsgControllerGroup3');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $request = $this->createRequest('GET', "/groups/{$group->getId()}/messages", $user->getToken());
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $messages = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($messages, 'Expected an array');
        $this->assertCount(0, $messages, 'Expected no messages in an empty group.');
    }


    // Listing messages using the since parameter to check only newer messages are returned.
    // Expected result: Only newer messages after the given timestamp.
    public function testListMessagesSince()
    {
        $user = User::create('msgControllerUser4');
        $group = Group::create('SinceGroup4');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        // Create an old message
        $msg1 = Message::create($group->getId(), $user->getId(), 'Old message');
        $oldTimestamp = $msg1->getTimestamp();

        // Wait 2 seconds so the next message definitely has a greater timestamp
        sleep(2);

        // Create a new message
        $msg2 = Message::create($group->getId(), $user->getId(), 'New message');

        // Now perform the GET request with the oldTimestamp
        $request = $this->createRequest('GET', "/groups/{$group->getId()}/messages?since={$oldTimestamp}", $user->getToken());
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode(), 'Expected 200 OK');
        $messages = json_decode((string)$response->getBody(), true);

        // We expect only the newer message after oldTimestamp
        $this->assertCount(1, $messages, 'Expected only newer messages after the given timestamp.');
        $this->assertEquals('New message', $messages[0]['message']);
    }
}