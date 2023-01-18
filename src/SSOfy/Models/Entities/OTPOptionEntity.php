<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string type
 * @property string hint
 */
class OTPOptionEntity extends BaseModel
{
    protected $properties = [
        'id',
        'type',
        'hint',
    ];

    protected $required = [
        'id',
        'type',
        'hint',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'id':
            case 'type':
            case 'hint':
                if (!is_string($value)) {
                    return 'value must be string.';
                }

                break;
        }

        return parent::validate($attr, $value);
    }
}
