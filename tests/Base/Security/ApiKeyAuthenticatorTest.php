<?php

declare(strict_types=1);

namespace tests\Base\Security;

use AppBundle\Security\{
    ApiKeyAuthenticator,
    ApiKeyUserProvider
};
use Prophecy\Prophet;
use Symfony\Component\HttpFoundation\{
    HeaderBag,
    Request
};
use Symfony\Component\Security\Core\{
    Authentication\Token\PreAuthenticatedToken,
    Authentication\Token\TokenInterface,
    Exception\UsernameNotFoundException,
    User\UserInterface,
    User\UserProviderInterface
};
use PHPUnit\Framework\TestCase;

class ApiKeyAuthenticatorTest extends TestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    protected $prophet;

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    public function testItDoesntSupportOtherTokens()
    {
        $token = $this->prophet->prophesize(TokenInterface::class);

        $authenticator = new ApiKeyAuthenticator();
        $this->assertFalse($authenticator->supportsToken($token->reveal(), 'abc'));
    }

    public function testItDoesntSupportIProvidersDontMatch()
    {
        $token = $this->prophet->prophesize(PreAuthenticatedToken::class);
        $token->getProviderKey()->willReturn('def');

        $authenticator = new ApiKeyAuthenticator();
        $this->assertFalse($authenticator->supportsToken($token->reveal(), 'abc'));
    }

    public function testItSupportsPreauthenticatedTokenWithMatchingProviders()
    {
        $token = $this->prophet->prophesize(PreAuthenticatedToken::class);
        $token->getProviderKey()->willReturn('abc');

        $authenticator = new ApiKeyAuthenticator();
        $this->assertTrue($authenticator->supportsToken($token->reveal(), 'abc'));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testItThrowsAnExceptionIfApiKeyIsNotSet(): void
    {
        $request = $this->prophet->prophesize(Request::class)->reveal();
        $headerBag = $this->prophet->prophesize(HeaderBag::class);
        $headerBag->get('apikey')->willReturn(null);
        $request->headers = $headerBag->reveal();

        $authenticator = new ApiKeyAuthenticator();
        $authenticator->createToken($request, 'abc');
    }

    public function testItCreatesValidToken(): void
    {
        $request = $this->prophet->prophesize(Request::class)->reveal();
        $headerBag = $this->prophet->prophesize(HeaderBag::class);
        $headerBag->get('apikey')->willReturn(12345);
        $request->headers = $headerBag->reveal();

        $authenticator = new ApiKeyAuthenticator();
        $this->assertEquals(new PreAuthenticatedToken('anon.', 12345, 'abc'), $authenticator->createToken($request, 'abc'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsAnExceptionIfApiKeyUserProviderIsNotPassed(): void
    {
        $userProvider = $this->prophet->prophesize(UserProviderInterface::class)->reveal();
        $token = $this->prophet->prophesize(TokenInterface::class)->reveal();

        $authenticator = new ApiKeyAuthenticator();
        $authenticator->authenticateToken($token, $userProvider, 'abc');
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testItThrowsAnExceptionIfApiKeyIsNotKnown()
    {
        $userProvider = $this->prophet->prophesize(ApiKeyUserProvider::class);
        $token = $this->prophet->prophesize(TokenInterface::class);

        $token->getCredentials()->willReturn(12345);
        $userProvider->loadUserByUsername(12345)->willThrow(new UsernameNotFoundException);

        $authenticator = new ApiKeyAuthenticator();
        $authenticator->authenticateToken($token->reveal(), $userProvider->reveal(), 'abc');
    }

    public function testItReturnsAuthenticatedToken()
    {
        $userProvider = $this->prophet->prophesize(ApiKeyUserProvider::class);
        $token = $this->prophet->prophesize(TokenInterface::class);
        $user = $this->prophet->prophesize(UserInterface::class);

        $token->getCredentials()->willReturn(12345);
        $user->getRoles()->willReturn(['ROLE_WRITE']);
        $userProvider->loadUserByUsername(12345)->willReturn($user->reveal());

        $authenticator = new ApiKeyAuthenticator();
        $this->assertEquals(new PreAuthenticatedToken(
            $user->reveal(),
            12345,
            'abc',
            ['ROLE_WRITE']
        ),$authenticator->authenticateToken($token->reveal(), $userProvider->reveal(), 'abc'));
    }
}
