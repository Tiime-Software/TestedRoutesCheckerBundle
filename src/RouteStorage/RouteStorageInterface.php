<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\RouteStorage;

/**
 * @internal
 */
interface RouteStorageInterface
{
    public function saveRoute(string $route): void;

    /**
     * @return string[]
     */
    public function getRoutes(): array;
}
