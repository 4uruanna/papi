<?php

namespace Papi\error;

use Exception;

class NotImplementedException extends Exception
{
    public function __construct()
    {
        return parent::__construct("Not implemented exception", 500);
    }
}
