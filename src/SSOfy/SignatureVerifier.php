<?php

namespace SSOfy;

use SSOfy\Models\Signature;

class SignatureVerifier
{
    /**
     * @var SignatureGenerator
     */
    protected $signatureGenerator;

    public function __construct(SignatureGenerator $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $signature
     * @return boolean
     */
    public function verifyBase64Signature($url, $params, $secret, $signature)
    {
        try {
            $decodedSignature   = new Signature(json_decode(base64_decode($signature), true));
            $generatedSignature = $this->signatureGenerator->generate($url, $params, $secret, $decodedSignature->salt);
            return $generatedSignature->hash === $decodedSignature->hash;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
