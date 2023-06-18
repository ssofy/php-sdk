<?php

use PHPUnit\Framework\TestCase;
use SSOfy\Models\Entities\ClientEntity;
use SSOfy\Models\Entities\ScopeEntity;
use SSOfy\Models\Signature;
use SSOfy\Models\Entities\UserEntity;
use SSOfy\SignatureGenerator;
use SSOfy\SignatureVerifier;

class SignatureValidatorTest extends TestCase
{
    public function test_signature_validator()
    {
        $cases = [
            [
                'url'       => 'https://example.com/external/ssofy/client',
                'params'    =>
                    [
                        ClientEntity::make([
                            'id'     => 'test-client',
                            'name'   => 'Test Client',
                            'secret' => 'cvg7oVzKM6g6Z4Nm',
                        ])->toArray(),
                    ],
                'signature' => base64_encode(json_encode(Signature::make([
                    'hash' => 'e3a375e05b73cb7ceede92e1b43f8369015375dc4a20f6ccc89b880740f75328',
                    'salt' => 'Py2BZIGgY'
                ])->toArray())),
            ],
            [
                'url'       => 'https://example.com/external/ssofy/scopes',
                'params'    => [
                    'scopes' => [
                        ScopeEntity::make([
                            'id'    => '*',
                            'title' => 'everything',
                        ])->toArray(),
                        ScopeEntity::make([
                            'id'    => 'profile',
                            'title' => 'profile',
                        ])->toArray(),
                    ]
                ],
                'signature' => base64_encode(json_encode(Signature::make([
                    'hash' => '9b3084c44f162dee2349c8682e8ba5b94f141a8ceb9bdce4cf82e5eab845c635',
                    'salt' => 'qHzBkp'
                ])->toArray())),
            ],
            [
                'url'       => 'https://example.com/external/ssofy/user',
                'params'    => [
                    'user' => UserEntity::make([
                        'id'           => 'test-user',
                        'hash'         => 'test-user',
                        'display_name' => 'test@example.com',
                    ])->toArray(),
                ],
                'signature' => base64_encode(json_encode(Signature::make([
                    'hash' => '2128e658770e9e5292f01a0dd52e766cd48c143240a10671a9cca83a60e3d204',
                    'salt' => 'y4HWL'
                ])->toArray())),
            ],
        ];

        $signatureGenerator = new SignatureGenerator();
        $validator = new SignatureVerifier($signatureGenerator);

        foreach ($cases as $case) {
            $ok = $validator->verifyBase64Signature($case['url'], $case['params'], 'lXp2rNYg8ht75l2l1vxNGNz2PWzZ7h6K', $case['signature']);
            $this->assertTrue($ok);
        }
    }
}
