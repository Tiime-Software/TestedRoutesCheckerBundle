<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\RouteStorage;

/**
 * @internal
 */
final class FileRouteStorage implements RouteStorageInterface
{
    public function __construct(
        private readonly string $file,
    ) {
    }

    #[\Override]
    public function saveRoute(string $route): void
    {
    }

    #[\Override]
    public function getRoutes(): array
    {
        if (!file_exists($this->file)) {
            throw new \InvalidArgumentException("File \"{$this->file}\"does not exists, did you correclty run tests?");
        }

        if (false === $routes = @file($this->file, \FILE_IGNORE_NEW_LINES)) {
            throw new \RuntimeException('Unable to load routes from given file.');
        }

        return $routes;
    }
}
