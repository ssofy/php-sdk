<?php

namespace SSOfy\Exceptions;

class RefreshTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct('Token is not renewable');
    }
}
