<?php

namespace Papi\Test\foo;

use Papi\ApiModule;
use Papi\Test\foo\services\FooService;

use function DI\create;

class FooModule extends ApiModule
{
    public function __construct()
    {
        $this->definition_list = [
            FooService::class => create()->constructor()
        ];
    }
}
