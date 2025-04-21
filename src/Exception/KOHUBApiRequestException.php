<?php

namespace DP0\Kohub\Exception;

use Exception;

class KOHUBApiRequestException extends Exception
{
    protected $data = [];

    public function __construct($message = "KOHUB API request failed", $code = 0, array $data = [])
    {

        parent::__construct($this->message, $this->code);
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}