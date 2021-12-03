<?php

declare(strict_types=1);

namespace AppBundle\Security;

class ApiKeyStore
{
    /**
     * @var array
     */
    private $readKeys;
    /**
     * @var array
     */
    private $writeKeys;

    /**
     * ApiKeyStore constructor.
     * @param array $readKeys
     * @param array $writeKeys
     */
    public function __construct(array $readKeys, array $writeKeys)
    {
        $this->readKeys = $readKeys;
        $this->writeKeys = $writeKeys;
    }

    public function hasReadKey($key): bool
    {
        return array_search($key, $this->readKeys) !== false;
    }

    public function hasWriteKey($key): bool
    {
        return array_search($key, $this->writeKeys) !== false;
    }
}
