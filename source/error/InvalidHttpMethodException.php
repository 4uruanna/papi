<?php

namespace Papi\error;

use Exception;

class InvalidHttpMethodException extends Exception
{
    public function __construct(string $action_name, string $http_method)
    {
        return parent::__construct("Invalid http method \"{$http_method}\" for action \"{$action_name}\"", 500);
    }
}
