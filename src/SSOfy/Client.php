<?php

namespace SSOfy;

use SSOfy\Storage\StorageInterface;
use SSOfy\Storage\NullStorage;
use SSOfy\Exceptions\APIException;
use SSOfy\Exceptions\InvalidTokenException;
use SSOfy\Exceptions\SignatureVerificationException;
use SSOfy\Models\APIResponse;
use SSOfy\Models\Token;
use SSOfy\Models\UserEntity;

class Client
{
    /**
     * @var ClientConfig
     */
    private $config;

    /**
     * @var StorageInterface
     */
    private $cache;

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

        $this->cache = empty($config->cacheStore()) ? new NullStorage() : $config->cacheStore();

        $this->signatureGenerator = new SignatureGenerator($config);
    }

    /**
     * @param string $token
     * @return APIResponse
     * @throws APIException
     * @throws InvalidTokenException
     * @throws SignatureVerificationException
     */
    public function verifyAuthentication($token)
    {
        $path  = 'v1/authenticated/verify';
        $token = $this->sanitizeToken($token);

        $response = $this->requestAndCache($path, $token);

        return new APIResponse([
            'token' => new Token($response['token'])
        ]);
    }

    /**
     * @param string $token
     * @param boolean $cache
     * @return APIResponse
     * @throws APIException
     * @throws InvalidTokenException
     * @throws SignatureVerificationException
     */
    public function authenticatedUser($token, $cache = false)
    {
        $path  = 'v1/authenticated/user';
        $token = $this->sanitizeToken($token);

        $response = $this->requestAndCache($path, $token, [], $cache);

        return new APIResponse([
            'user'  => new UserEntity($response['user']),
            'token' => new Token($response['token'])
        ]);
    }

    /**
     * @param string $id
     * @param boolean $cache
     * @return APIResponse
     * @throws APIException
     * @throws InvalidTokenException
     * @throws SignatureVerificationException
     */
    public function findUserById($id, $cache = false)
    {
        $path = "v1/entities/users/find";

        $response = $this->requestAndCache($path, null, [
            'id' => $id
        ], $cache);

        return new APIResponse([
            'user' => new UserEntity($response['user']),
        ]);
    }

    /**
     * @param string $token
     * @return void
     */
    public function invalidateTokenCache($token)
    {
        $this->cache->delete("v1/authenticated/verify:$token");
        $this->cache->delete("v1/authenticated/user:$token");
    }

    /**
     * @return void
     */
    public function purgeTokenCache()
    {
        $this->cache->flushAll();
    }

    /**
     * @param string $path
     * @param null|string $token
     * @param array $fields
     * @param null|boolean $cache
     * @return array
     * @throws APIException
     * @throws InvalidTokenException
     * @throws SignatureVerificationException
     */
    private function requestAndCache($path, $token = null, $fields = [], $cache = true)
    {
        $cacheKey = "request:$path:$token";

        if ($cache) {
            // try the cache first
            $cached = $this->cache->get($cacheKey);
            if (!is_null($cached)) {
                if (empty($cached)) {
                    throw new InvalidTokenException();
                }

                return json_decode($cached, true);
            }
            //
        }

        try {
            if (!empty($token)) {
                $fields['bearer'] = $token;
            }

            $response = $this->request($path, $fields, true);

            $parsed = json_decode($response['body'], true);

            // response signature verification
            $signature          = isset($response['headers']['signature']) && $response['headers']['signature'][0] ? $response['headers']['signature'][0] : null;
            $signatureValidator = new SignatureValidator($this->config);
            if (
                empty($signature) ||
                !$signatureValidator->verifyBase64Signature(Helper::urlJoin('http://localhost', $path), $parsed, $signature)
            ) {
                throw new SignatureVerificationException();
            }
            //

            if ($cache) {
                $ttl = 0;

                if (isset($parsed['token'])) {
                    $ttl = (new \DateTime($parsed['token']['expires_at']))->getTimestamp() - time();
                    $ttl = max(1, $ttl); // token ttl should not be eternal
                }

                $this->cache->put($cacheKey, $response['body'], min($ttl, $this->config->cacheTtl()));
            }

            return $parsed;
        } catch (InvalidTokenException $exception) {
            if ($cache) {
                // cache the failure result to avoid repetitive requests to server
                $this->cache->put($cacheKey, '', $this->config->cacheTtl());
            }

            throw $exception;
        }
    }

    /**
     * @param string $path
     * @param array $fields
     * @param boolean $post
     * @return array
     * @throws APIException
     * @throws InvalidTokenException
     * @throws SignatureVerificationException
     */
    private function request($path, $fields = [], $post = false)
    {
        $protocol = $this->config->secure() ? 'https://' : 'http://';

        $url = $protocol . $this->config->domain();
        $url = Helper::urlJoin($url, $path);

        $salt      = Helper::randomString(rand(16, 32));
        $signature = base64_encode(json_encode($this->signatureGenerator->generate($url, $fields, $salt)->toArray()));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept: application/json',
                'Api-Key: ' . $this->config->key(),
                'Signature: ' . $signature,
            ]
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_NOPROGRESS, true);

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        }

        // this function is called by curl for each header received
        $headers = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$headers) {
            $len    = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) {
                return $len;
            }

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        });

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch ($httpCode) {
            case 200:
                return [
                    'body'    => $body,
                    'headers' => $headers,
                ];

            case 401:
                throw new InvalidTokenException();

            case 400:
                throw new SignatureVerificationException();

            default:
                throw new APIException();
        }
    }

    private function sanitizeToken($token)
    {
        $arr = explode(' ', strval($token));
        return end($arr);
    }
}
