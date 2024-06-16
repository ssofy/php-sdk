<?php

namespace SSOfy\Models;

use SSOfy\Models\Entities\UserEntity;

/**
 * @property Token token
 * @property UserEntity user
 */
class APIResponse extends BaseModel
{
    protected $properties = [
        'token',
        'user',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'token':
                if (!is_a($value, Token::class)) {
                    return 'value must be ' . Token::class;
                }

                return true;

            case 'user':
                if (!is_a($value, UserEntity::class)) {
                    return 'value must be ' . UserEntity::class;
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
