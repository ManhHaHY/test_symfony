<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\{Model\Task,Form\TaskType};
use FOS\RestBundle\{Controller\FOSRestController,View\View};
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\{Request,Response};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class TasksController extends FOSRestController
{
    /**
     * @TODO Add cache annotation
     * @Cache(expires="+60 seconds", public=false)
     */
    public function getTaskAction(Task $task) : Task
    {
        return $task;
    }

    public function deleteTaskAction(Task $task) : View
    {
        dump($task);die;
        $this->get('task_repository')->delete($task);
        return new View([], Response::HTTP_OK);
    }

    /**
     * @TODO Add cache annotation
     * @Cache(expires="+30 seconds", public=false, maxAge=30)
     */
    public function getTasksAction() : View
    {
        return new View($this->get('task_repository')->findAll());
    }

    public function postTasksAction(Request $request) : View
    {
        return $this->processTask(new Task(), $request, 'POST', Response::HTTP_CREATED);
    }

    public function putTaskAction(Task $task, Request $request) : View
    {
        return $this->processTask($task, $request, 'PUT', Response::HTTP_OK);
    }

    private function processTask(Task $task, Request $request, string $verb, int $successResponseCode) : View
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->container->get('form.factory');
        $form = $formFactory->createNamed('', TaskType::class, $task, ['method' => $verb]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return new View($task, Response::HTTP_UNPROCESSABLE_ENTITY);
        };

        $this->get('task_repository')->save($task);

        return new View($task, $successResponseCode);
    }
}
