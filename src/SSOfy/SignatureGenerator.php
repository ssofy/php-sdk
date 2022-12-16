<?php

namespace SSOfy;

use SSOfy\Models\Signature;

class SignatureGenerator
{
    private $key;
    private $secret;

    /**
     * @param ClientConfig $config
     */
    public function __construct($config)
    {
        $this->key    = $config->key();
        $this->secret = $config->secret();
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $salt
     * @return Signature
     */
    public function generate($url, $params, $salt = null)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);

        $toSign = $urlPath . implode('', $this->getValues($params)) . $this->key . $this->secret . $salt;

        $hash = openssl_digest($toSign, "sha256");

        return new Signature([
            'hash' => $hash,
            'salt' => $salt,
        ]);
    }

    private function getValues($array, $values = [])
    {
        ksort($array);

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $values = $this->getValues($value, $values);
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
