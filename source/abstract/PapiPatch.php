<?php

namespace Papi\abstract;

use Papi\enumerator\HttpMethod;
use Papi\interface\PapiAction;

abstract class PapiPatch implements PapiAction
{
    public static function getMethod(): HttpMethod
    {
        return HttpMethod::PATCH;
    }
}
