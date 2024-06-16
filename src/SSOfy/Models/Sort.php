<?php

namespace SSOfy\Models;

use SSOfy\Enums\SortOrder;

/**
 * @property string field
 * @property string order
 */
class Sort extends BaseModel
{
    protected $properties = [
        'key',
        'order',
    ];

    protected $required = [
        'key',
    ];

    protected $defaults = [
        'order' => SortOrder::ASCENDING,
    ];

    protected function validate($attr, $value)
    {
        switch ($attr) {
            case 'order':
                $orders = [
                    SortOrder::ASCENDING,
                    SortOrder::DESCENDING,
                ];

                if (!in_array($value, $orders)) {
                    return 'value must be one of: ' . implode(', ', $orders);
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
