<?php

declare(strict_types=1);

namespace tests\Base\Security;

use AppBundle\Security\{
    ApiKeyStore,
    ApiKeyUserProvider
};
use Prophecy\Prophet;
use Symfony\Component\Security\Core\User\{
    User,
    UserInterface
};
use PHPUnit\Framework\TestCase;

class ApiKeyUserProviderTest extends TestCase
{
    /**
     * @var Prophet
     */
    private $prophet;

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    public function testItSupportsValidUserClass()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $this->assertTrue($userProvider->supportsClass(User::class));
    }

    public function testItDoesntSupportsInvalidUserClass()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $this->assertFalse($userProvider->supportsClass(UserInterface::class));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testItThrowsAnExceptionIfApiKeyUserProviderIsNotPassed()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);
        $user = $this->prophet->prophesize(UserInterface::class);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $userProvider->refreshUser($user->reveal());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testItThrowsAnExceptionIfApiKeyDoesNotExist()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);
        $apiKeyStore->hasReadKey(12345)->willReturn(false);
        $apiKeyStore->hasWriteKey(12345)->willReturn(false);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $userProvider->loadUserByUsername(12345);
    }

    public function testItReturnsAnInstanceOfUserWithReadRole()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);
        $apiKeyStore->hasWriteKey(12345)->willReturn(false);
        $apiKeyStore->hasReadKey(12345)->willReturn(true);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $this->assertEquals(new User(
            12345,
            null,
            ['ROLE_READ']
        ), $userProvider->loadUserByUsername(12345));
    }

    public function testItReturnsAnInstanceOfUserWithWriteRole()
    {
        $apiKeyStore = $this->prophet->prophesize(ApiKeyStore::class);
        $apiKeyStore->hasWriteKey(12345)->willReturn(true);

        $userProvider = new ApiKeyUserProvider($apiKeyStore->reveal());
        $this->assertEquals(new User(
            12345,
            null,
            ['ROLE_WRITE']
        ), $userProvider->loadUserByUsername(12345));
    }
}
