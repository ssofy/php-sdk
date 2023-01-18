<?php

namespace SSOfy\Models;

use SSOfy\Models\Entities\UserEntity;

/**
 * @property Token token
 * @property UserEntity $user
 */
class APIResponse extends BaseModel
{
    protected $properties = [
        'token',
        'user',
    ];
}
