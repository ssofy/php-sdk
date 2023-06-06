<?php

use PHPUnit\Framework\TestCase;
use SSOfy\APIClient;
use SSOfy\APIConfig;

class ClientTest extends TestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = new APIClient(new APIConfig([
            'domain' => 'test.api.ssofy.local',
            'key'    => 'cf47d697-cc0b-4262-8329-78a0995e6fd0',
            'secret' => 'lXp2rNYg8ht75l2l1vxNGNz2PWzZ7h6K',
            'cache'  => null,
            'ttl'    => 3,
            'secure' => false,
        ]));
    }

    /**
     * @group skip
     */
    public function test_token_verification()
    {
        $response = $this->client->verifyAuthentication('0184f38cfe53715880bdc64415face01ea401c6a0c2b4da0a1f98a2104c7a7e1');

        print(PHP_EOL);
        print(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->assertIsBool(true);
    }

    /**
     * @group skip
     */
    public function test_resource_owner()
    {
        $response = $this->client->authenticatedUser('0184f38cfe53715880bdc64415face01ea401c6a0c2b4da0a1f98a2104c7a7e1');

        print(PHP_EOL);
        print(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->assertIsBool(true);
    }
}
