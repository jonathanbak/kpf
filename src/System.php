<?php

namespace Kpf;


use Kpf\Exception\Exception;

abstract class System
{

    public function __construct()
    {
        if (!$this->isCli()) {
            throw new Exception("Access Denied.");
        }
    }

    protected function isCli(): bool
    {
        return php_sapi_name() === "cli";
    }
}