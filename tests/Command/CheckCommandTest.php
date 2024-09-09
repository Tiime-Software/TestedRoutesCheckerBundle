<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests\RouteStorage;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Tiime\TestedRoutesCheckerBundle\Command\CheckCommand;
use Tiime\TestedRoutesCheckerBundle\RoutesChecker;

final class CheckCommandTest extends TestCase
{
    public function testWithoutUntestedRoutes(): void
    {
        $routesChecker = $this->createMock(RoutesChecker::class);
        $routesChecker->expects($this->once())->method('getUntestedRoutes')->willReturn([]);

        $commandTester = new CommandTester(new CheckCommand($routesChecker, 10, __DIR__.'/ignored_routes'));

        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Congrats, all routes have been tested!', $output);
    }

    public function testWithUntestedRoutes(): void
    {
        $routesChecker = $this->createMock(RoutesChecker::class);
        $routesChecker->expects($this->once())->method('getUntestedRoutes')->willReturn(['route1', 'route2']);

        $commandTester = new CommandTester(new CheckCommand($routesChecker, 10, __DIR__.'/ignored_routes'));

        $commandTester->execute([]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[ERROR] Found 2 non tested routes!', $output);
    }
}
