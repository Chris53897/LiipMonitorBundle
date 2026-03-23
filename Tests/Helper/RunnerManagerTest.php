<?php

namespace Liip\MonitorBundle\Tests\Helper;

use Liip\MonitorBundle\Helper\RunnerManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;

class RunnerManagerTest extends \PHPUnit\Framework\TestCase
{
    private Container|MockObject $container;

    /**
     * @var RunnerManager
     */
    private $runnerManager;

    public function groupProvider(): array
    {
        return [
            [null],
            ['default'],
            ['test'],
        ];
    }

    /**
     * @dataProvider groupProvider
     *
     * @param string $group
     */
    public function testGetRunner($group): void
    {
        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('liip_monitor.default_group')
            ->willReturn('default');

        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('liip_monitor.runner_'.($group ?: 'default'))
            ->willReturn(true);

        $expectedResult = $this->getMockBuilder('Liip\MonitorBundle\Runner')->getMock();

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('liip_monitor.runner_'.($group ?: 'default'))
            ->willReturn($expectedResult);

        $result = $this->runnerManager->getRunner($group);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetRunnerReturnsNull(): void
    {
        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('liip_monitor.runner_testgroup')
            ->willReturn(false);

        $result = $this->runnerManager->getRunner('testgroup');

        $this->assertNull($result);
    }

    public function testGetRunners(): void
    {
        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('liip_monitor.runners')
            ->willReturn(['liip_monitor.runner_group_1', 'liip_monitor.runner_group_2']);

        $runnerMockBuilder = $this->getMockBuilder('Liip\MonitorBundle\Runner');
        $runner1 = $runnerMockBuilder->getMock();
        $runner2 = $runnerMockBuilder->getMock();
        $this->container
            ->expects($invokedCount = $this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($parameter) use ($invokedCount, $runner1, $runner2) {
                if (1 === $invokedCount->numberOfInvocations()) {
                    $this->assertSame('liip_monitor.runner_group_1', $parameter);

                    return $runner1;
                }
                $this->assertSame('liip_monitor.runner_group_2', $parameter);

                return $runner2;
            })
        ;

        $result = $this->runnerManager->getRunners();

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('group_1', $result);
        $this->assertArrayHasKey('group_2', $result);
        $this->assertSame($runner1, $result['group_1']);
        $this->assertSame($runner2, $result['group_2']);
    }

    public function testGetGroups(): void
    {
        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('liip_monitor.runners')
            ->willReturn(['liip_monitor.runner_group_1', 'liip_monitor.runner_group_2']);

        $result = $this->runnerManager->getGroups();

        $this->assertCount(2, $result);
        $this->assertContains('group_1', $result);
        $this->assertContains('group_2', $result);
    }

    public function testGetDefaultGroup(): void
    {
        $expectedResult = 'default';

        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('liip_monitor.default_group')
            ->willReturn($expectedResult);

        $result = $this->runnerManager->getDefaultGroup();

        $this->assertEquals($expectedResult, $result);
    }

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();

        $this->runnerManager = new RunnerManager($this->container);
    }
}
