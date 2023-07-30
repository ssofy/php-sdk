<?php

namespace SSOfy\Exceptions;

class InvalidValueException extends Exception
{
    public function __construct($attr, $message)
    {
        parent::__construct(trim("\"{$attr}\" is invalid. {$message}"));
    }
}
