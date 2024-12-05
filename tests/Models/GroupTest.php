<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\Group;

class GroupTest extends BaseTestCase
{
    public function testGroupCreation()
    {
        $groupName = 'Test Group';
        $group = Group::create($groupName);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertNotEmpty($group->getId());
        $this->assertEquals($groupName, $group->getName());
    }

    public function testFindGroupById()
    {
        $groupName = 'Test Group';
        $group = Group::create($groupName);

        $foundGroup = Group::findById($group->getId());

        $this->assertInstanceOf(Group::class, $foundGroup);
        $this->assertEquals($group->getId(), $foundGroup->getId());
        $this->assertEquals($group->getName(), $foundGroup->getName());
    }
}