<?php

namespace SSOfy\Exceptions;

class AuthErrorException extends Exception
{
    /**
     * @param string|null $message
     */
    public function __construct($message)
    {
        parent::__construct("Error: ${message}");
    }
}
