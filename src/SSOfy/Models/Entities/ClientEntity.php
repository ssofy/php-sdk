<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string name
 * @property string secret
 * @property string[] redirect_uris
 * @property string icon
 * @property string theme
 * @property string tos
 * @property string privacy_policy
 * @property boolean confidential
 */
class ClientEntity extends BaseModel
{
    protected $properties = [
        'id',
        'name',
        'secret',
        'redirect_uris',
        'icon',
        'theme',
        'tos',
        'privacy_policy',
        'confidential',
    ];

    protected $required = [
        'id',
        'name',
        'secret',
    ];

    protected $defaults = [
        'redirect_uris' => ['*'],
        'confidential'  => false,
    ];

    /**
     * @param string $uri
     * @return ClientEntity
     */
    public function addRedirectUri($uri)
    {
        $redirectUris   = $this->redirect_uris;
        $redirectUris[] = $uri;

        $this->redirect_uris = $redirectUris;

        return $this;
    }

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'confidential':
                if (!is_bool($value)) {
                    return 'value must be boolean';
                }

                return true;

            case 'redirect_uris':
                if (!is_array($this->values['redirect_uris'])) {
                    return 'value must be array';
                }

                if (array_keys($this->values['redirect_uris']) !== range(0, count($this->values['redirect_uris']) - 1)) {
                    return 'value must be an indexed array';
                }

                foreach ($value as $item) {
                    if (!is_string($item)) {
                        return 'value must be an array of strings';
                    }
                }

                return true;
        }

        return parent::validate($attr, $value);
    }

    public function export($requiredFieldsCheck = true)
    {
        $export = parent::export($requiredFieldsCheck);

        $export['redirect_uris'] = array_values($this->values['redirect_uris']);

        return $export;
    }
}
