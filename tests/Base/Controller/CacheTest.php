<?php

declare(strict_types=1);

namespace tests\Base\Controller;

class CacheTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->getContainer()->get('task_repository')->deleteAll();
        $this->apiKey = 'qwerty';
    }

    public function testCacheHeaderSetForList(): void
    {
        $this->jsonRequest('GET', '/api/tasks');

        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertSame('max-age=30, private', $this->client->getResponse()->headers->get('Cache-Control'));
    }
}
