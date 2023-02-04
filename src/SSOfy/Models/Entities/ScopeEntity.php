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
}
