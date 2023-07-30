<?php

namespace SSOfy\Exceptions;

class SignatureVerificationException extends Exception
{
    public function __construct()
    {
        parent::__construct('Signature verification failed');
    }
}
