<?php

use PHPUnit\Framework\TestCase;
use SSOfy\ClientConfig;
use SSOfy\Models\ClientEntity;
use SSOfy\Models\ScopeEntity;
use SSOfy\Models\Signature;
use SSOfy\Models\UserEntity;
use SSOfy\SignatureValidator;

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
                    'hash' => 'c6f9f6eb5868af271bcaae915a515bbefb5e46f4e87a41596270b357b5627f64',
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
                    'hash' => 'c0100920478966fbd8650b10e98ad552a2787a97b51ff77bf4339daa218ddc90',
                    'salt' => 'qHzBkp'
                ])->toArray())),
            ],
            [
                'url'       => 'https://example.com/external/ssofy/user',
                'params'    => [
                    'user' => UserEntity::make([
                        'id'           => 'test-user',
                        'display_name' => 'test@example.com',
                    ])->toArray(),
                ],
                'signature' => base64_encode(json_encode(Signature::make([
                    'hash' => '599f54743dde85838520851fc550285bc0d8365bf02c6d2f9a96da635dbd6a72',
                    'salt' => 'y4HWL'
                ])->toArray())),
            ],
        ];

        $validator = new SignatureValidator(new ClientConfig([
            'key'    => 'cf47d697-cc0b-4262-8329-78a0995e6fd0',
            'secret' => 'lXp2rNYg8ht75l2l1vxNGNz2PWzZ7h6K',
        ]));

        foreach ($cases as $case) {
            $ok = $validator->verifyBase64Signature($case['url'], $case['params'], $case['signature']);
            $this->assertTrue($ok);
        }
    }
}
