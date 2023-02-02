<?php

namespace App\Exceptions;

class RequestException extends \Exception
{
    protected $resultCode;

    function __construct($msg = '未查询到相关信息', $resultCode = 1)
    {
        $this->resultCode = $resultCode;
        parent::__construct($msg);
    }

    public function getResultCode()
    {
        return $this->resultCode;
    }
}