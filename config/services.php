<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tiime\TestedRoutesCheckerBundle\Command\CheckCommand;
use Tiime\TestedRoutesCheckerBundle\RoutesChecker;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\FileRouteStorage;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('tiime_tested_routes_checker_bundle.routes_checker', RoutesChecker::class)
            ->args([
                service('router'),
                service('tiime_tested_routes_checker_bundle.route_storage.file'),
            ])

        ->set('tiime_tested_routes_checker_bundle.route_storage.file', FileRouteStorage::class)
            ->args([
                param('tiime_tested_routes_checker_bundle.route_storage_file'),
            ])

        ->set('tiime_tested_routes_checker_bundle.command.check', CheckCommand::class)
            ->args([
                service('tiime_tested_routes_checker_bundle.routes_checker'),
                param('tiime_tested_routes_checker_bundle.maximum_number_of_routes_to_display'),
                param('tiime_tested_routes_checker_bundle.routes_to_ignore_file'),
            ])
            ->tag('console.command')
    ;
};
