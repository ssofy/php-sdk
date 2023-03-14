<?php

namespace SSOfy\Models;

/**
 * @property string token
 * @property string[] scopes
 * @property string user_id
 * @property string client_id
 * @property string expires_at
 */
class Token extends BaseModel
{
    protected $properties = [
        'token',
        'scopes',
        'user_id',
        'client_id',
        'expires_at',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'scopes':
                if (!is_array($value)) {
                    return 'value must be array';
                }

                return true;
        }

        return parent::validate($attr, $value);
    }

    public function export()
    {
        $export = parent::export();

        $export['expires_at'] = \DateTime::createFromFormat(DATE_ATOM, $this->expires_at);

        return $export;
    }
}
