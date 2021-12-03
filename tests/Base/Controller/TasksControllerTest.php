<?php

declare(strict_types=1);

namespace tests\Base\Controller;

use AppBundle\Model\Task;

class TasksControllerTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->getContainer()->get('task_repository')->deleteAll();
        $this->apiKey = 'qwerty';
    }

    public function testEmptyTaskList(): void
    {
        $this->jsonRequest('GET', '/api/tasks');

        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertSame([], json_decode($this->client->getResponse()->getContent()));
    }

    public function testListingOneTask(): void
    {
        $task = new Task();
        $task->setTitle('Test title');
        $task->setDescription('Test desc');

        $this->client->getContainer()->get('task_repository')->save($task);

        $this->jsonRequest('GET', '/api/tasks');

        $this->assertJsonResponse($this->client->getResponse(), 200);
        $returnedTask = [['id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'completed' => $task->getCompleted()
        ]];
        $this->assertEquals($returnedTask, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testPost(): void
    {
        $this->jsonRequest('POST', '/api/tasks', ['title' => 'Test Title', 'description' => 'Test Description', 'completed' => false]);

        $this->assertJsonResponse($this->client->getResponse(), 201);
        $task = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('Test Title', $task['title']);
        $this->assertEquals('Test Description', $task['description']);
        $this->assertEquals(false, $task['completed']);

        $this->jsonRequest('GET', '/api/tasks');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testDelete(): void
    {
        $task = new Task();
        $task->setTitle('Test title');
        $task->setDescription('Test desc');

        $this->client->getContainer()->get('task_repository')->save($task);

        $this->jsonRequest('DELETE', '/api/tasks/'.$task->getId());

        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertEquals([], json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testPut(): void
    {
        $task = new Task();
        $task->setTitle('Test title');
        $task->setDescription('Test desc');

        $this->client->getContainer()->get('task_repository')->save($task);

        $this->jsonRequest('PUT', '/api/tasks/'.$task->getId(), ['title' => 'New Title', 'description' => 'New Title', 'completed' => true]);

        $this->assertJsonResponse($this->client->getResponse(), 200);
        $updatedTask = ['id' => $task->getId(),
            'title' => 'New Title',
            'description' => 'New Title',
            'completed' => true
        ];
        $this->assertEquals($updatedTask, json_decode($this->client->getResponse()->getContent(), true));

        $this->jsonRequest('GET', '/api/tasks');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent(), true));
    }
}
