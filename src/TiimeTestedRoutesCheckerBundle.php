<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TiimeTestedRoutesCheckerBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /* @phpstan-ignore-next-line */
        $definition->rootNode()
            ->children()
                ->integerNode('maximum_number_of_routes_to_display')->defaultValue(25)->end()
                ->scalarNode('route_storage_file')->defaultValue('%kernel.project_dir%/var/cache/tiime_tested_routes_checker_bundle_route_storage')->end()
            ->end()
        ;
    }

    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        if ('test' === $container->env()) {
            $container->import('../config/services_test.php');
        }

        $container->parameters()->set('tiime_tested_routes_checker_bundle.maximum_number_of_routes_to_display', $config['maximum_number_of_routes_to_display']);
        $container->parameters()->set('tiime_tested_routes_checker_bundle.route_storage_file', $config['route_storage_file']);
    }
}
