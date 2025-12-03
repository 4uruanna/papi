<?php

declare(strict_types=1);

namespace Papi\Test;

use Papi\Module;
use Papi\Test\foo\FooModule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(Module::class)]
#[Small]
final class ModuleTest extends TestCase
{
    public function testModule(): void
    {
        $module = new FooModule();

        $this->assertEquals(0, count($module->getDefinitions()));
        $this->assertEquals(1, count($module->getEvents()));
        $this->assertEquals(1, count($module->getMiddlewares()));
        $this->assertEquals(1, count($module->getActions()));
    }
}
