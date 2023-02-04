<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string type
 * @property string to
 * @property string hint
 * @property string user_id
 * @property string action
 */
class OTPOptionEntity extends BaseModel
{
    protected $properties = [
        'id',
        'type',
        'to',
        'hint',
        'user_id',
        'action',
    ];

    protected $required = [
        'id',
        'type',
        'to',
        'hint',
        'user_id',
        'action',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'id':
            case 'type':
            case 'to':
            case 'hint':
            case 'user_id':
            case 'action':
                if (!is_string($value)) {
                    return 'value must be string.';
                }

                break;
        }

        return parent::validate($attr, $value);
    }
}
