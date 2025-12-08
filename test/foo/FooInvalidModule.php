<?php

namespace Papi\Test\foo;

use Papi\ApiModule;

class FooInvalidModule extends ApiModule
{
    public array|null $prerequisite_list = ["Papi\\Missing\\FooClassName"];
}
