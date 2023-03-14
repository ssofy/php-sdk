<?php

namespace SSOfy\Models;

/**
 * @property string hash
 * @property string salt
 */
class Signature extends BaseModel
{
    protected $properties = [
        'hash',
        'salt',
    ];

    protected $required = [
        'hash',
        'salt',
    ];
}
