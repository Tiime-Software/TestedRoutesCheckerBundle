<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Tiime\TestedRoutesCheckerBundle\RoutesChecker;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

final class RoutesCheckerTest extends TestCase
{
    private RouterInterface&MockObject $router;
    private RouteStorageInterface&MockObject $routeStorage;
    private RoutesChecker $routesChecker;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->routeStorage = $this->createMock(RouteStorageInterface::class);

        $this->routesChecker = new RoutesChecker($this->router, $this->routeStorage);
    }

    public function testGetTestedIgnoredRoutes(): void
    {
        $this->routeStorage->expects($this->once())
                ->method('getRoutes')
                ->willReturn(['route1', 'route2']);

        $testedIgnoredRoutes = $this->routesChecker->getTestedIgnoredRoutes(['route2', 'route3']);

        $this->assertSame(['route2'], $testedIgnoredRoutes);
    }
}
