<?php

namespace SSOfy;

use SSOfy\Models\Signature;

class SignatureGenerator
{
    /**
     * @param string $url
     * @param array  $params
     * @param string $secret
     * @param string $salt
     * @return Signature
     */
    public function generate($url, $params, $secret, $salt = null)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);

        $toSign = $urlPath . implode('', $this->getValues($params)) . $salt;

        $hash = hash_hmac("sha256", $toSign, $secret);

        $signature       = new Signature();
        $signature->hash = $hash;
        $signature->salt = $salt;

        return $signature;
    }

    private function getValues($array)
    {
        $values = [];

        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                $values = array_merge($values, $this->getValues($value));
                continue;
            }

            if (is_bool($value)) {
                $values[] = $value ? '1' : '0';
                continue;
            }

            $values[] = strval($value);
        }

        return $values;
    }
}
