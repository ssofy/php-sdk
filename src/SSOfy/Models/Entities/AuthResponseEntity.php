<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property UserEntity user
 * @property TokenEntity token
 */
class AuthResponseEntity extends BaseModel
{
    protected $properties = [
        'user',
        'token',
    ];

    protected $required = [
        'user',
    ];

    protected $defaults = [];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'user':
                if (!$value instanceof UserEntity) {
                    return 'value must be UserEntity.';
                }

                return true;

            case 'token':
                if (!$value instanceof TokenEntity) {
                    return 'value must be TokenEntity.';
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
