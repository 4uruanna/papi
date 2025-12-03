<?php

declare(strict_types=1);

require_once __DIR__ . "/foo/FooModule.php";

use Papi\Module\Module;
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

        $this->assertEquals(false, $module->getEvents());
        $this->assertEquals(3, count($module->getDefinitions()));
        $this->assertEquals(2, count($module->getMiddlewares()));
        $this->assertEquals(1, count($module->getRoutes()));
    }
}
