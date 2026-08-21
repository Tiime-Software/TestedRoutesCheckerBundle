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
        $definition
            ->rootNode()
            ->children()
                ->integerNode('maximum_number_of_routes_to_display')->defaultValue(25)->end()
                ->scalarNode('routes_to_ignore_file')->defaultValue('%kernel.project_dir%/.tiime-trc-baseline')->end()
                ->scalarNode('route_storage_file')->defaultValue('%kernel.project_dir%/var/cache/tiime_tested_routes_checker_bundle_route_storage')->end()
            ->end()
        ;
    }

    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import('../config/services.php');

        if ('test' === $configurator->env()) {
            $configurator->import('../config/services_test.php');
        }

        $configurator->parameters()->set('tiime_tested_routes_checker_bundle.maximum_number_of_routes_to_display', $config['maximum_number_of_routes_to_display']);
        $configurator->parameters()->set('tiime_tested_routes_checker_bundle.routes_to_ignore_file', $config['routes_to_ignore_file']);
        $configurator->parameters()->set('tiime_tested_routes_checker_bundle.route_storage_file', $config['route_storage_file']);
    }
}
