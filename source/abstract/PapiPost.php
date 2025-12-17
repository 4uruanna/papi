<?php

namespace Papi\abstract;

use Papi\enumerator\HttpMethod;
use Papi\interface\PapiAction;

abstract class PapiPost implements PapiAction
{
    public static function getMethod(): HttpMethod
    {
        return HttpMethod::POST;
    }
}
