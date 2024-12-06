<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class GroupMemberTest extends BaseTestCase
{
    // Test adding a user to a group.
    // Expected result: Should return true indicating the user was successfully added to the group.
    public function testAddUserToGroup()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');

        $result = GroupMember::addUserToGroup($user->getId(), $group->getId());

        $this->assertTrue($result);
    }

    // Test checking if a user is a member of a specific group.
    // Expected result: Should return true if the user is a member of the group.
    public function testIsUserInGroup()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');

        GroupMember::addUserToGroup($user->getId(), $group->getId());

        $isInGroup = GroupMember::isUserInGroup($user->getId(), $group->getId());

        $this->assertTrue($isInGroup);
    }

    // Test adding a user who is already a member of the group.
    // Expected result: Should throw an exception.
    public function testAddUserAlreadyMember()
    {
        $user = User::create('testuser');
        $group = Group::create('Test Group');

        GroupMember::addUserToGroup($user->getId(), $group->getId());
        $this->expectException(\Exception::class);
        GroupMember::addUserToGroup($user->getId(), $group->getId());
    }


    // Test adding a non-existent user or group.
    // Expected result: If user or group doesn't exist, should fail.
    public function testAddNonExistentUserOrGroup()
    {
        // Non-existent user with ID 9999 and a real group
        $group = Group::create('RealGroup');
        $result = GroupMember::addUserToGroup(9999, $group->getId());
        $this->assertFalse($result, 'Expected false when adding non-existent user.');

        // Real user but non-existent group
        $user = User::create('testuser2');
        $result = GroupMember::addUserToGroup($user->getId(), 9999);
        $this->assertFalse($result, 'Expected false when adding user to a non-existent group.');
    }

    // Test isUserInGroup() for a user or group that doesn’t exist.
    // Expected result: false.
    public function testIsUserInGroupNonExistent()
    {
        $this->assertFalse(GroupMember::isUserInGroup(9999, 9999), 'Expected false for non-existent user and group.');
    }
}