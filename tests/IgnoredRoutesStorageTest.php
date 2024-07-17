<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tiime\TestedRoutesCheckerBundle\IgnoredRoutesStorage;

final class IgnoredRoutesStorageTest extends TestCase
{
    public function testStorage(): void
    {
        $storage = new IgnoredRoutesStorage(__DIR__.'/../var/cache/test_ignored_routes');

        $storage->saveRoute('route1');
        $storage->saveRoute('route2');
        $storage->saveRoute('route3');
        $storage->saveRoute('route2');

        $this->assertSame(['route1', 'route2', 'route3'], $storage->getRoutes());
    }
}
