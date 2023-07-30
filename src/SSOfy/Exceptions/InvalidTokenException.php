<?php

namespace SSOfy\Exceptions;

class InvalidTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid or Expired token');
    }
}
