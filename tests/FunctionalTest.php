<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Tiime\TestedRoutesCheckerBundle\Command\CheckCommand;

final class FunctionalTest extends TestCase
{
    public function testBundleInit(): void
    {
        $kernel = new TestKernel([
            'framework' => [
                'http_method_override' => false,
                'handle_all_throwables' => true,
                'php_errors' => [
                    'log' => true,
                ],
                'router' => [
                    'resource' => '',
                ],
            ],
        ]);
        $kernel->boot();

        $container = $kernel->getContainer();
        $this->assertInstanceOf(Container::class, $container);

        $removedServices = array_keys($container->getRemovedIds());
        $this->assertTrue(\in_array('tiime_tested_routes_checker_bundle.command.check', $removedServices));

        $command = $container->get(CheckCommand::class);
        $this->assertInstanceOf(CheckCommand::class, $command);
    }
}
