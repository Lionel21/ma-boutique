<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    /**
     * @var User
     */
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User();
    }

    public function testGetEmail()
    {
        $value = 'test@test.com';

        $this->assertInstanceOf(User::class, $this->user->setEmail($value));
        $this->assertEquals($value, $this->user->getEmail());
        $this->assertEquals($value, $this->user->getUsername());
    }

    public function testGetRoles()
    {
        $value = ['ROLE_ADMIN'];

        $response = $this->user->setRoles($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertContains('ROLE_USER', $this->user->getRoles());
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

}