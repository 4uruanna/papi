<?php

namespace Papi\Test\foo;

use Papi\AppBuilder;

use function DI\create;

return [
    AppBuilder::class => create()->constructor()
];
