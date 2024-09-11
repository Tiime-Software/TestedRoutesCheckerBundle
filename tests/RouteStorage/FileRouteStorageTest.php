<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests\RouteStorage;

use PHPUnit\Framework\TestCase;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\FileRouteStorage;

final class FileRouteStorageTest extends TestCase
{
    public function testStorage(): void
    {
        $storage = new FileRouteStorage(__DIR__.'/../../var/cache/test_cache_file_'.bin2hex(random_bytes(5)));

        $storage->saveRoute('route1', 200);
        $storage->saveRoute('route2', 500);
        $storage->saveRoute('route3', 403);
        $storage->saveRoute('route2', 401);

        $this->assertSame([
            'route1' => [200],
            'route2' => [500, 401],
            'route3' => [403],
        ], $storage->getRoutes());
    }
}
