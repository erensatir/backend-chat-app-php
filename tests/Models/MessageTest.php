<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Models\GroupMember;

class MessageTest extends BaseTestCase
{
    public function testCreateMessage()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $messageText = 'Hello, world!';
        $message = Message::create($group->getId(), $user->getId(), $messageText);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertNotEmpty($message->getId());
        $this->assertEquals($messageText, $message->getMessage());
        $this->assertEquals($user->getId(), $message->getUserId());
        $this->assertEquals($group->getId(), $message->getGroupId());
    }

    public function testGetMessagesByGroup()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        Message::create($group->getId(), $user->getId(), 'First message');
        Message::create($group->getId(), $user->getId(), 'Second message');

        $messages = Message::getMessagesByGroup($group->getId());

        $this->assertCount(2, $messages);
    }

    
    // Test creating a message with an empty string as message text.
    // Expected result: Should throw exception.
    public function testCreateMessageWithEmptyText()
    {
        $user = User::create('msguser');
        $group = Group::create('MsgGroup');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $this->expectException(\Exception::class);
        Message::create($group->getId(), $user->getId(), '');
    }

    
    // Test retrieving messages from a group with no messages.
    // Expected result: Should return an empty array.
    public function testGetMessagesFromEmptyGroup()
    {
        $user = User::create('nomsguser');
        $group = Group::create('EmptyMsgGroup');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $messages = Message::getMessagesByGroup($group->getId());
        $this->assertCount(0, $messages, 'Expected an empty array for a group with no messages.');
    }

    
    // Test using the since parameter to fetch newer messages.
    // Expected result: Only messages after the given timestamp are returned.
    public function testGetMessagesByGroupSince()
    {
        $user = User::create('sinceuser');
        $group = Group::create('SinceGroup');
        GroupMember::addUserToGroup($user->getId(), $group->getId());

        // Create a message now
        $msg1 = Message::create($group->getId(), $user->getId(), 'Old Message');

        // Sleep to ensure a different timestamp or manipulate timestamps in tests
        sleep(1);

        // Create another message
        $msg2 = Message::create($group->getId(), $user->getId(), 'New Message');

        // Fetch messages since msg1's timestamp
        $messages = Message::getMessagesByGroup($group->getId(), $msg1->getTimestamp());
        $this->assertCount(1, $messages, 'Should only return the newer message.');
        $this->assertEquals('New Message', $messages[0]->getMessage());
    }

    
    // Test create() with invalid group/user IDs.
    // Expected result: Should fail or throw an exception.
    public function testCreateMessageWithInvalidIDs()
    {
        $this->expectException(\Exception::class);
        Message::create(9999, 9999, 'Invalid IDs');
    }
}