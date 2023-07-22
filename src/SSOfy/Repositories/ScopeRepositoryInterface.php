<?php

namespace SSOfy\Repositories;

use SSOfy\Models\Entities\ScopeEntity;

interface ScopeRepositoryInterface
{
    /**
     * Get list of available OAuth2 Scopes.
     *
     * @param string $lang
     * @return ScopeEntity[]
     */
    public function all($lang);
}
