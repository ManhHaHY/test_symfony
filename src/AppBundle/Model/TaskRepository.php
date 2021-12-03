<?php

declare(strict_types=1);

namespace AppBundle\Model;

use Everzet\PersistedObjects\{AccessorObjectIdentifier,FileRepository};

class TaskRepository
{
    private $fileRepository;

    /**
     * TaskRepository constructor.
     * @param string $cacheDir
     */
    public function __construct(string $cacheDir)
    {
        $this->fileRepository = new FileRepository($cacheDir . '/tasks', new AccessorObjectIdentifier('getId'));
    }

    public function save(Task $task): void
    {
        $this->fileRepository->save($task);
    }

    public function findById($taskId)
    {
        return $this->fileRepository->findById($taskId);
    }

    public function findAll(): array
    {
        return $this->fileRepository->getAll();
    }

    public function delete(Task $task): void
    {
        $this->fileRepository->remove($task);
    }

    public function deleteAll(): void
    {
        $this->fileRepository->clear();
    }
}
