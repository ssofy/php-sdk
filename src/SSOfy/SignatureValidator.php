<?php

namespace SSOfy;

use SSOfy\Models\Signature;

class SignatureValidator
{
    /**
     * @var ClientConfig
     */
    private $config;

    /**
     * @var SignatureGenerator
     */
    private $signatureGenerator;

    /**
     * @param ClientConfig $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->signatureGenerator = new SignatureGenerator();
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $signature
     * @return boolean
     */
    public function verifyBase64Signature($url, $params, $signature)
    {
        try {
            $decodedSignature   = new Signature(json_decode(base64_decode($signature), true));
            $generatedSignature = $this->signatureGenerator->generate($url, $params, $this->config->getSecret(), $decodedSignature->salt);
            return $generatedSignature->hash === $decodedSignature->hash;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
