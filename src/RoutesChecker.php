<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle;

use Symfony\Component\Routing\RouterInterface;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

class RoutesChecker
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

        return $this->filterRoutesWithRoutesToIgnore($untestedRoutes, $routesToIgnore);
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
     * Return not successfully tested routes.
     *
     * A route is considered non fully tested when it never return a successful
     * code during test execution.
     *
     * - a successful code is a 1xx, 2xx or 3xx code.
     * - if the route have been tested several times with at least 1 successful
     * code: it's considered successfully tested.
     *
     * @param string[] $routesToIgnore
     *
     * @return string[]
     */
    public function getNotSuccessfullyTestedRoutes(array $routesToIgnore = []): array
    {
        // Filter all routes wich have been successfully tested at least once.
        $routes = array_keys(array_filter($this->routeStorage->getRoutes(), static function (array $responseCodes): bool {
            foreach ($responseCodes as $responseCode) {
                if ($responseCode < 400) {
                    return false;
                }
            }

            return true;
        }));

        return $this->filterRoutesWithRoutesToIgnore($routes, $routesToIgnore);
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

    /**
     * @param string[] $routes
     * @param string[] $routesToIgnore
     *
     * @return string[]
     */
    private function filterRoutesWithRoutesToIgnore(array $routes, array $routesToIgnore): array
    {
        $filteredRoutes = [];
        foreach ($routes as $route) {
            if (\in_array($route, $routesToIgnore)) {
                continue;
            }
            foreach ($routesToIgnore as $routeToIgnore) {
                if (@preg_match("#\b$routeToIgnore\b#", $route)) {
                    continue 2;
                }
            }

            $filteredRoutes[] = $route;
        }

        return $filteredRoutes;
    }
}
