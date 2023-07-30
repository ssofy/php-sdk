<?php

namespace SSOfy\Exceptions;

class InvalidStateException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid State');
    }
}
