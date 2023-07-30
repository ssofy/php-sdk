<?php

namespace SSOfy\Models\Entities;

use SSOfy\Models\BaseModel;

/**
 * @property string id
 * @property string hash
 * @property string display_name
 * @property string name
 * @property string picture
 * @property string profile
 * @property string email
 * @property string username
 * @property bool email_verified
 * @property string phone
 * @property bool phone_verified
 * @property string given_name
 * @property string middle_name
 * @property string family_name
 * @property string nickname
 * @property string website
 * @property string gender
 * @property string birthdate
 * @property string address
 * @property string location
 * @property string zoneinfo
 * @property string locale
 * @property string custom_1
 * @property string custom_2
 * @property string custom_3
 * @property string custom_4
 * @property string custom_5
 * @property string custom_6
 * @property string custom_7
 * @property string custom_8
 * @property string custom_9
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
        'username',
        'email',
        'email_verified',
        'phone',
        'phone_verified',
        'given_name',
        'middle_name',
        'family_name',
        'nickname',
        'website',
        'gender',
        'birthdate',
        'address',
        'location',
        'zoneinfo',
        'locale',
        'custom_1',
        'custom_2',
        'custom_3',
        'custom_4',
        'custom_5',
        'custom_6',
        'custom_7',
        'custom_8',
        'custom_9',
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
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return 'value must be email';
                }

                return true;

            case 'phone':
                if (substr($value, 0, 1) != '+' || strlen($value) < 10 || strlen($value) > 20) {
                    return 'value must be E164 phone number';
                }

                return true;

            case 'email_verified':
            case 'phone_verified':
                if (!is_bool($value)) {
                    return 'value must be boolean';
                }

                return true;

            case 'gender':
                if (!in_array($value, ['male', 'female'])) {
                    return 'value must be "male" or "female"';
                }

                return true;

            case 'birthdate':
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    return 'value must be in Y-m-d format';
                }

                return true;

            case 'location':
                if (!preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)([^\d]|$),[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)([^\d]|$)/', $value)) {
                    return 'value must be a valid comma separated coordination';
                }

                return true;

            case 'zoneinfo':
                if (!preg_match('/^[A-Za-z0-9_\-+\/]+$/', $value)) {
                    return 'value must be a valid timezone';
                }

                return true;

            case 'locale':
                if (!preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $value)) {
                    return 'value must be a valid locale';
                }

                return true;

            case 'additional':
                if (!is_array($this->values['additional'])) {
                    return 'value must be array';
                }

                if (array_keys($this->values['additional']) === range(0, count($this->values['additional']) - 1)) {
                    return 'value must be an associative array';
                }

                foreach ($value as $key => $val) {
                    if (!is_string($key)) {
                        return 'array must be in key-value format';
                    }
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
