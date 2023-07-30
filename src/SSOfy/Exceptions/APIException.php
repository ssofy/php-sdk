<?php

namespace SSOfy\Exceptions;

class APIException extends Exception
{
    public function __construct()
    {
        parent::__construct('Cannot communicate with API. Check your API Key or Url.');
    }
}
