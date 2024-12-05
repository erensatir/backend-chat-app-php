<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\User;

class UserTest extends BaseTestCase
{
    public function testUserCreation()
    {
        $username = 'testuser';
        $user = User::create($username);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertNotEmpty($user->getToken());
    }

    public function testFindUserByToken()
    {
        $username = 'testuser';
        $user = User::create($username);

        $foundUser = User::findByToken($user->getToken());

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
        $this->assertEquals($user->getUsername(), $foundUser->getUsername());
    }
}