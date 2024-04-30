<?php

declare(strict_types=1);

namespace Symfony\Bundle\DebugBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\DebugBundle\DependencyInjection\DebugExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;

class TiimeTestedRoutesCheckerBundleTest extends TestCase
{
}
