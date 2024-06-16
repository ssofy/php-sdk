<?php

namespace SSOfy\Models;

/**
 * @property array data
 * @property int page
 * @property int page_size
 * @property int total_pages
 * @property int total_count
 */
class PaginatedResponse extends BaseModel
{
    protected $properties = [
        'data',
        'page',
        'page_size',
        'total_pages',
        'total_count',
    ];

    protected $required = [
        'data',
        'page',
        'page_size',
        'total_pages',
        'total_count',
    ];

    protected function validate($attr, $value)
    {
        if (is_null($value)) {
            return true;
        }

        switch ($attr) {
            case 'data':
                if (!is_array($value)) {
                    return 'value must be array';
                }

                return true;

            case 'page':
            case 'page_size':
            case 'total_pages':
            case 'total_count':
                if (!is_int($value)) {
                    return 'value must be integer';
                }

                return true;
        }

        return parent::validate($attr, $value);
    }
}
