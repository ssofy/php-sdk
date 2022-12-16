<?php

namespace SSOfy\Models;

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
