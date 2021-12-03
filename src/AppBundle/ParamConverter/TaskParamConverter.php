<?php

declare(strict_types=1);

namespace AppBundle\ParamConverter;

use AppBundle\Model\{Task,TaskRepository};
use Sensio\Bundle\FrameworkExtraBundle\{Configuration\ParamConverter,Request\ParamConverter\ParamConverterInterface};
use Symfony\Component\HttpFoundation\Request;

class TaskParamConverter implements ParamConverterInterface
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * TaskParamConverter constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * {@inheritdoc}
     *
     * Check, if object supported by our converter
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === Task::class;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        /**
         * @TODO Implement it:
         * - should throw InvalidArgumentException if there is no 'task' key in $request->attributes
         * - should throw Symfony\Component\HttpKernel\Exception\NotFoundHttpException if task was not found in repository (to find it you need to use id stored in $request->attributes->get('task'))
         * - if task was found set it on $request->attributes, the key name should come from $configuration->getName()
         * - return true if everything went ok
         */
        if ($request->attributes->get('task') == null){
            throw new \InvalidArgumentException('Not have task.');
        }
        $id = $request->attributes->get('task');
        $task = $this->taskRepository->findById($id);

        if(!$task) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $request->attributes->set($configuration->getName(), $task);

        return true;
    }
}
