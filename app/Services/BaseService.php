<?php

namespace App\Services;

abstract class BaseService
{
    protected $data;

    public function setParams($data = null)
    {
        $this->data = $data;

        return $this;
    }

    abstract public function handle();
}
