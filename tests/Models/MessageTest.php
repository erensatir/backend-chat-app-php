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
}