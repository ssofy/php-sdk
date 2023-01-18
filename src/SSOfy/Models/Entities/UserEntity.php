<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string display_name
 * @property string name
 * @property string picture
 * @property string profile
 * @property array additional
 */
class UserEntity extends BaseModel
{
    protected $properties = [
        'id',
        'hash',
        'display_name',
        'name',
        'picture',
        'profile',
        'additional',
    ];

    protected $required = [
        'id',
        'hash',
    ];

    protected $defaults = [
        'additional' => []
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'id':
            case 'display_name':
            case 'name':
            case 'picture':
            case 'profile':
                if (!is_string($value)) {
                    return 'value must be string.';
                }

                break;

            case 'additional':
                if (!is_array($this->values['additional'])) {
                    return 'value must be array.';
                }

                if (array_keys($this->values['additional']) === range(0, count($this->values['additional']) - 1)) {
                    return 'value must be an associative array.';
                }

                foreach ($value as $key => $val) {
                    if (!is_string($key)) {
                        return 'array must be in key-value format.';
                    }
                }

                break;
        }

        return parent::validate($attr, $value);
    }
}
