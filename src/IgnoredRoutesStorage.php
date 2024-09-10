<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle;

/**
 * @internal
 */
final class IgnoredRoutesStorage
{
    public function __construct(
        private readonly string $file,
    ) {
    }

    public function reset(): void
    {
        file_put_contents($this->file, '');
    }

    public function saveRoute(string $route): void
    {
        if (!file_exists($this->file)) {
            touch($this->file);
        }

        file_put_contents($this->file, "$route\n", \FILE_APPEND);
    }

    /**
     * @param string[] $routes
     */
    public function saveRoutes(array $routes): void
    {
        if (!file_exists($this->file)) {
            touch($this->file);
        }

        file_put_contents($this->file, implode(\PHP_EOL, $routes), \FILE_APPEND);
    }

    /**
     * @return string[]
     */
    public function getRoutes(): array
    {
        if (!file_exists($this->file)) {
            throw new \InvalidArgumentException("File \"{$this->file}\"does not exists, unable to load ignored routes!");
        }

        if (false === $routes = @file($this->file, \FILE_IGNORE_NEW_LINES)) {
            throw new \RuntimeException('Unable to load ignored routes from given file.');
        }

        return array_values(array_unique($routes));
    }
}
