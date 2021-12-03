<?php

declare(strict_types=1);

namespace tests\Base\Controller;

use AppBundle\Model\Task;

class SecurityTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider getUnauthorisedRequests
     */
    public function testUnauthorisedRequests(string $verb, string $uri): void
    {
        $this->jsonRequest($verb, $uri);
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function getUnauthorisedRequests(): array
    {
        return [
            ['GET', '/api/tasks'],
            ['POST', '/api/tasks'],
        ];
    }

    public function testAuthorisedRequestsForReadKey(): void
    {
        $this->apiKey = 12345;
        $this->jsonRequest('GET', '/api/tasks');
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $this->client->getContainer()->get('task_repository')->deleteAll();
        $task = new Task();
        $task->setTitle('Test title');
        $task->setDescription('Test desc');
        $this->client->getContainer()->get('task_repository')->save($task);

        $this->jsonRequest('GET', '/api/tasks/' . $task->getId());
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    /**
     * @dataProvider getUnauthorisedRequestsForReadKey
     */
    public function testUnauthorisedRequestsForReadKey(string $verb, string $uri): void
    {
        $this->apiKey = 12345;
        $this->jsonRequest($verb, $uri);
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function getUnauthorisedRequestsForReadKey(): array
    {
        return [
            ['POST', '/api/tasks']
        ];
    }
}
