<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tiime\TestedRoutesCheckerBundle\Command\CheckCommand;
use Tiime\TestedRoutesCheckerBundle\TiimeTestedRoutesCheckerBundle;

class TestKernel extends Kernel
{
    /** @param array<string, array<string, mixed>|null> $config */
    public function __construct(
        private readonly array $config,
    ) {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TiimeTestedRoutesCheckerBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            foreach ($this->config as $extension => $config) {
                $container->loadFromExtension($extension, $config);
            }
        });
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->setAlias(CheckCommand::class, 'tiime_tested_routes_checker_bundle.command.check')->setPublic(true);
            }
        });
    }
}
