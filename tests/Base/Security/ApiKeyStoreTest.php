<?php

declare(strict_types=1);

namespace tests\Base\Security;

use AppBundle\Security\ApiKeyStore;
use PHPUnit\Framework\TestCase;

class ApiKeyStoreTest extends TestCase
{
    public function testItStoresKeys(): void
    {
        $store = new ApiKeyStore(['12345'], ['qwerty']);
        $this->assertFalse($store->hasWriteKey(12345));
        $this->assertFalse($store->hasReadKey('qwerty'));
        $this->assertTrue($store->hasWriteKey('qwerty'));
        $this->assertTrue($store->hasReadKey(12345));
    }
}
