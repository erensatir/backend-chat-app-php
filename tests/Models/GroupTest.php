<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\Group;

class GroupTest extends BaseTestCase
{
    // Test the creation of a group with a valid name.
    // Expected result: Should successfully create a Group instance, assign it a valid ID, and set the correct name.
    public function testGroupCreation()
    {
        $groupName = 'Test Group';
        $group = Group::create($groupName);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertNotEmpty($group->getId());
        $this->assertEquals($groupName, $group->getName());
    }

    // Test finding a group by its ID.
    // Expected result: Should return a Group instance with the correct ID and name.
    public function testFindGroupById()
    {
        $groupName = 'Test Group';
        $group = Group::create($groupName);

        $foundGroup = Group::findById($group->getId());

        $this->assertInstanceOf(Group::class, $foundGroup);
        $this->assertEquals($group->getId(), $foundGroup->getId());
        $this->assertEquals($group->getName(), $foundGroup->getName());
    }

    
    // Test creating a group with an empty name.
    // Expected result: Should fail or return an error.
    public function testCreateGroupWithEmptyName()
    {
        $this->expectException(\Exception::class);
        Group::create('');
    }

    // Test creating a group with a name that already exists.
    // Expected result: Should fail or throw an exception.
    public function testCreateGroupWithExistingName()
    {
        Group::create('UniqueGroup');
        $this->expectException(\Exception::class);
        Group::create('UniqueGroup');
    }
    
    // Test findById() with a non-existent ID.
    // Expected result: Should return null if the group does not exist.
    public function testFindByNonExistentId()
    {
        $group = Group::findById(9999);
        $this->assertNull($group, 'Expected null when finding a non-existent group by ID.');
    }
}