<?php

namespace Kpf;


use Kpf\Exception\Exception;

abstract class System
{

    public function __construct()
    {
        if(php_sapi_name() != "cli"){
            throw new Exception("Access Denied.");
        }
    }
}