<?php

namespace SSOfy\Exceptions;

class RequiredAttributeException extends Exception
{
    public function __construct($attr)
    {
        parent::__construct("$attr is required");
    }
}
