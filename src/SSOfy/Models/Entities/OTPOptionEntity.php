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
}
