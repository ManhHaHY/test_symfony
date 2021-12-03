<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Symfony\Component\Security\Core\{
    Exception\UnsupportedUserException,
    Exception\UsernameNotFoundException,
    User\User,
    User\UserInterface,
    User\UserProviderInterface
};

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var ApiKeyStore
     */
    private $apiKeyStore;

    /**
     * ApiKeyUserProvider constructor.
     * @param ApiKeyStore $apiKeyStore
     */
    public function __construct(ApiKeyStore $apiKeyStore = null)
    {
        if (!$apiKeyStore) {
            throw new \InvalidArgumentException('ApiKeyStore instance was not provided');
        }
        $this->apiKeyStore = $apiKeyStore;
    }

    public function loadUserByUsername($username): UserInterface
    {
        if ($this->apiKeyStore->hasWriteKey($username)) {
            return new User(
                $username,
                null,
                ['ROLE_WRITE']
            );
        } elseif ($this->apiKeyStore->hasReadKey($username)) {
            return new User(
                $username,
                null,
                ['ROLE_READ']
            );
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
