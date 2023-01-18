<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string title
 * @property string description
 * @property string icon
 * @property string url
 */
class ScopeEntity extends BaseModel
{
    protected $properties = [
        'id',
        'title',
        'description',
        'icon',
        'url',
    ];

    protected $required = [
        'id',
        'title',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'id':
            case 'title':
            case 'description':
            case 'icon':
            case 'url':
                if (!is_string($value)) {
                    return 'value must be string.';
                }

                break;
        }

        return parent::validate($attr, $value);
    }
}
