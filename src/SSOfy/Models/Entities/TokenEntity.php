<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string token
 * @property int ttl
 */
class TokenEntity extends BaseModel
{
    protected $properties = [
        'token',
        'ttl',
    ];

    protected $required = [
        'token',
        'ttl',
    ];

    protected $defaults = [
        'ttl' => 0,
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'token':
                if (!is_string($value)) {
                    return 'value must be string.';
                }

                return true;

            case 'ttl':
                if (!is_numeric($value)) {
                    return 'value must be integer.';
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
