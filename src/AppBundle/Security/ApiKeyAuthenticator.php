<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Security\Core\{
    Authentication\Token\PreAuthenticatedToken,
    Authentication\Token\TokenInterface,
    Exception\AuthenticationException,
    Exception\BadCredentialsException,
    User\UserProviderInterface
};
use Symfony\Component\Security\Http\Authentication\{
    AuthenticationFailureHandlerInterface,
    SimplePreAuthenticatorInterface
};

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey): TokenInterface
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();
        $user = $userProvider->loadUserByUsername($apiKey);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $providerKey): TokenInterface
    {
        $apiKey = $request->headers->get('apikey');

        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        /**
         * @TODO: Return Unauthorised JSON response with content "error" => "Authentication Failed."
         */
        return new JsonResponse([
            'error' => 'Authentication Failed.'
        ], 403);
    }
}
