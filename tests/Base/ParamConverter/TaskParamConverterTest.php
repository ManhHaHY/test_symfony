<?php

declare(strict_types=1);

namespace tests\Base\ParamConverter;

use AppBundle\{
    Model\Task,
    Model\TaskRepository,
    ParamConverter\TaskParamConverter
};
use Prophecy\Prophet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\{
    ParameterBag,
    Request
};

class TaskParamConverterTest extends TestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    protected $prophet;
    /**
     * @var TaskRepository
     */
    protected $repository;
    /**
     * @var TaskParamConverter
     */
    protected $paramConverter;
    /**
     * @var ParamConverter
     */
    protected $configuration;

    protected function setup()
    {
        $this->prophet = new Prophet();
        $this->repository = $this->prophet->prophesize(TaskRepository::class);
        $this->paramConverter = new TaskParamConverter($this->repository->reveal());
        $this->configuration = $this->prophet->prophesize(ParamConverter::class);
    }

    public function testItSupportsTaskClass(): void
    {
        $this->configuration->getClass()->willReturn(Task::class);
        $this->assertTrue($this->paramConverter->supports($this->configuration->reveal()));
    }

    public function testItDoesntSupportAnyOtherClass(): void
    {
        $configuration = $this->prophet->prophesize(ParamConverter::class);
        $configuration->getClass()->willReturn('stdClass');
        $this->assertFalse($this->paramConverter->supports($configuration->reveal()));
    }

    public function testItSetsRequestAsAnAttributeIfFoundInTheRepository(): void
    {
        $request = $this->prophet->prophesize(Request::class)->reveal();
        $task = $this->prophet->prophesize(Task::class)->reveal();
        $parameterBag = $this->prophet->prophesize(ParameterBag::class);
        $parameterBag->has('task')->willReturn(true);
        $parameterBag->get('task')->willReturn(1234);
        $request->attributes = $parameterBag->reveal();
        $this->repository->findById(1234)->willReturn($task);
        $this->configuration->getName()->willReturn('abc');

        $parameterBag->set('abc', $task)->shouldBeCalled();
        $this->assertTrue($this->paramConverter->apply($request, $this->configuration->reveal()));

        $this->prophet->checkPredictions();
    }
}
