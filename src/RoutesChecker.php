<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle;

use Symfony\Component\Routing\RouterInterface;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

final class RoutesChecker
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly RouteStorageInterface $routeStorage,
    ) {
    }

    /**
     * @param string[] $routesToIgnore
     *
     * @return string[]
     */
    public function getUntestedRoutes(array $routesToIgnore = []): array
    {
        $routesToIgnore = array_merge($this->getDefaultRoutesToIgnore(), $routesToIgnore);

        $testedRoutes = array_keys($this->routeStorage->getRoutes());

        $routes = array_keys($this->router->getRouteCollection()->all());
        $untestedRoutes = array_diff($routes, $testedRoutes);

        $filteredRoutes = [];
        foreach ($untestedRoutes as $untestedRoute) {
            if (\in_array($untestedRoute, $routesToIgnore)) {
                continue;
            }
            foreach ($routesToIgnore as $routeToIgnore) {
                if (@preg_match("#\b$routeToIgnore\b#", $untestedRoute)) {
                    continue 2;
                }
            }

            $filteredRoutes[] = $untestedRoute;
        }

        return $filteredRoutes;
    }

    /**
     * Return ignored routes which are tested.
     *
     * @param string[] $routesToIgnore
     *
     * @return string[]
     */
    public function getTestedIgnoredRoutes(array $routesToIgnore = []): array
    {
        $testedRoutes = array_keys($this->routeStorage->getRoutes());

        return array_values(array_intersect($testedRoutes, $routesToIgnore));
    }

    /**
     * @return string[]
     */
    private function getDefaultRoutesToIgnore(): array
    {
        return [
            '^_profiler.*$',
            '_wdt',
            '_webhook_controller',
            '_preview_error',
            'app.swagger',
        ];
    }
}
