<?php

namespace Papi\Test\mock;

final class BarDefinition
{
    private FooDefinition $foo;

    public function __construct(FooDefinition $foo)
    {
        $this->foo = $foo;
    }

    public function getBar(): bool
    {
        return 'B@r';
    }
}
