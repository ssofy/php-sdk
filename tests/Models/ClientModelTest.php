<?php

use PHPUnit\Framework\TestCase;
use SSOfy\Exceptions\RequiredAttributeException;
use SSOfy\Models\ClientEntity;

class ClientModelTest extends TestCase
{
    public function test_mass_assign()
    {
        $data = [
            'id'                 => 'id_test',
            'name'               => 'name_test',
            'secret'             => 'secret_test',
            'redirect_uris'      => ['uri_test'],
            'icon'               => 'icon_test',
            'theme'              => 'theme_test',
            'tos'                => 'tos_test',
            'privacy_policy'     => 'privacy_policy_test',
            'confidential'       => true,
        ];

        $model = new ClientEntity($data);

        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($model->toArray()));
    }

    public function test_required_properties()
    {
        $data = [
            'name' => 'name_test',
        ];

        $model = new ClientEntity($data);

        $this->expectException(RequiredAttributeException::class);

        $model->export();
    }

    public function test_unknown_attribute()
    {
        $data = [
            'id'     => 'id_test',
            'name'   => 'name_test',
            'secret' => 'secret_test',
            'dummy'  => 'dummy_test',
        ];

        $model = new ClientEntity($data);

        $this->assertTrue(true);
    }

    public function test_unknown_attribute_assignment()
    {
        $model     = new ClientEntity();
        $model->id = 'test_id';

        $model->dummy = 'dummy_test';

        $this->assertTrue(true);
    }
}
