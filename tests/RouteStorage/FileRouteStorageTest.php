<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests\RouteStorage;

use PHPUnit\Framework\TestCase;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\FileRouteStorage;

final class FileRouteStorageTest extends TestCase
{
    public function testStorage(): void
    {
        $this->markTestSkipped();
        // $storage = new FileRouteStorage(__DIR__.'/../../var/cache/test_cache_file');

        // $storage->saveRoute('route1');
        // $storage->saveRoute('route2');
        // $storage->saveRoute('route3');

        // $this->assertSame(['route1', 'route2', 'route3'], $storage->getRoutes());
    }
}
