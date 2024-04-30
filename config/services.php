<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tiime\TestedRoutesCheckerBundle\Command\CheckCommand;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('tiime_tested_routes_checker_bundle.command.check', CheckCommand::class)
            ->args([
                service('router'),
                param('tiime_tested_routes_checker_bundle.maximum_number_of_routes_to_display'),
            ])
            ->tag('console.command')
    ;
};
