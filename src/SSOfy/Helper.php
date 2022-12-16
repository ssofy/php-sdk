<?php

namespace SSOfy;

class Helper
{
    /**
     * @param int $length
     * @return string
     */
    public static function randomString($length = 10)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function snakeToCamel($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * @param string $url
     * @param string $path
     * @return string
     */
    public static function urlJoin($url, $path)
    {
        return rtrim($url, "/ \t\n\r\0\x0B") . '/' . ltrim($path, "/ \t\n\r\0\x0B");
    }

    /**
     * @param string $base
     * @param string $path
     * @return string
     */
    public static function pathJoin($base, $path)
    {
        return rtrim($base, "/ \t\n\r\0\x0B") . DIRECTORY_SEPARATOR . ltrim($path, "/ \t\n\r\0\x0B");
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function addUrlParams($url, $params)
    {
        $url = parse_url($url);

        $query = isset($url['query']) ? $url['query'] : '';
        $path  = isset($url['path']) ? $url['path'] : '';

        parse_str($query, $existingParams);

        $newQuery = array_merge($existingParams, $params);

        $newUrl = $url['scheme'] . '://' . $url['host'] . $path;
        if ($newQuery) {
            $newUrl .= '?' . http_build_query($newQuery);
        }

        if (isset($url['fragment'])) {
            $newUrl .= '#' . $url['fragment'];
        }

        return $newUrl;
    }
}
