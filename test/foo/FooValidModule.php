<?php

namespace Papi\Test\foo;

use Papi\ApiModule;

class FooValidModule extends ApiModule
{
    public array|null $prerequisite_list = [
        FooModule::class
    ];
}
