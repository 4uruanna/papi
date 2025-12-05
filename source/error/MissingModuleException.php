<?php

namespace Papi\error;

use Exception;

class MissingModuleException extends Exception
{
    public function __construct(string $module_name, string $missing_module = "")
    {
        return parent::__construct("Module \"{$missing_module}\" is required by \"{$module_name}\" ", 500);
    }
}
