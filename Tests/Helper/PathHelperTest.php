<?php

namespace Liip\MonitorBundle\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PathHelperTest extends WebTestCase
{
    public function testGenerateRoutes(): void
    {
        $client = static::createClient();

        $container = $client->getKernel()->getContainer();

        $pathHelper = $container->get('liip_monitor.helper');

        // test route is defined in Tests/app/routing.yml
        $routes = $pathHelper->generateRoutes(['test_route' => []]);

        $this->assertEquals(['api.test_route = "/monitor";'], $routes);
    }
}
