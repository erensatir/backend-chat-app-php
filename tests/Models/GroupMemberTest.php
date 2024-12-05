<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class GroupMemberTest extends BaseTestCase
{
    public function testAddUserToGroup()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');

        $result = GroupMember::addUserToGroup($user->getId(), $group->getId());

        $this->assertTrue($result);
    }

    public function testIsUserInGroup()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');

        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $isInGroup = GroupMember::isUserInGroup($user->getId(), $group->getId());

        $this->assertTrue($isInGroup);
    }
}