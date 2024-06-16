<?php

namespace SSOfy\Models;

use SSOfy\Enums\FilterOperator;

/**
 * @property string key
 * @property mixed value
 * @property string operator
 */
class Filter extends BaseModel
{
    protected $properties = [
        'key',
        'value',
        'operator',
    ];

    protected $required = [
        'key',
    ];

    protected $defaults = [
        'operator' => FilterOperator::EQUALS,
    ];

    protected function validate($attr, $value)
    {
        switch ($attr) {
            case 'value':
                if ($this->operator === FilterOperator::IN) {
                    if (!is_array($value)) {
                        return 'value must be array';
                    }
                }

                return true;

            case 'operator':
                $operators = [
                    FilterOperator::EQUALS,
                    FilterOperator::NOT_EQUALS,
                    FilterOperator::GREATER_THAN,
                    FilterOperator::GREATER_THAN_OR_EQUAL_TO,
                    FilterOperator::LESS_THAN,
                    FilterOperator::LESS_THAN_OR_EQUAL_TO,
                    FilterOperator::IN,
                    FilterOperator::CONTAINS,
                    FilterOperator::STARTS_WITH,
                    FilterOperator::ENDS_WITH,
                ];

                if (!in_array($value, $operators)) {
                    return 'value must be one of: ' . implode(', ', $operators);
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
