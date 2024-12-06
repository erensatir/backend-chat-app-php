<?php

namespace Tests\Models;

use Tests\BaseTestCase;
use App\Models\User;

class UserTest extends BaseTestCase
{
    // Test creating a user with a valid username.
    // Expected result: User instance is created with a valid ID, username, and token.
    public function testUserCreation()
    {
        $username = 'testuser';
        $user = User::create($username);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertNotEmpty($user->getToken());
    }
    
    // Test finding a user by their token.
    // Expected result: The user is successfully retrieved with the correct ID and username.
    public function testFindUserByToken()
    {
        $username = 'testuser';
        $user = User::create($username);

        $foundUser = User::findByToken($user->getToken());

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
        $this->assertEquals($user->getUsername(), $foundUser->getUsername());
    }

    
    // Test creating a user with an already taken token.
    // Expected result: Should throw an error.
    public function testCreateUserWithTakenToken()
    {
        // Create first user with a forced token
        User::create('testuser1', 'fixed_token');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Token already taken');
        
        // Attempt to create second user with the same token, should throw exception
        User::create('testuser2', 'fixed_token');
    }

    
    // Test findByToken() with a non-existent token.
    // Expected result: null.
    public function testFindByNonExistentToken()
    {
        $nonExistentToken = 'nonexistent123token';
        $user = User::findByToken($nonExistentToken);
        $this->assertNull($user, 'Expected findByToken() to return null for a non-existent token.');
    }

    public function testCreateUserWithDuplicateUsername()
{
    User::create('duplicateUser');
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Username already taken');

    // Attempt to create another user with the same username
    User::create('duplicateUser');
}

    
    // Test ensuring tokens are indeed unique.
    // Expected result: All tokens should be unique.
    public function testUniqueTokensForMultipleUsers()
    {
        $user1 = User::create('userA');
        $user2 = User::create('userB');
        $this->assertNotEquals($user1->getToken(), $user2->getToken(), 'Each user should have a unique token.');
    }
}