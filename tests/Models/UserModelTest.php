<?php

use PHPUnit\Framework\TestCase;
use SSOfy\Models\Entities\UserEntity;

class UserModelTest extends TestCase
{
    public function test_mass_assign()
    {
        $data = [
            'id'           => 'id_test',
            'hash'         => 'id_test',
            'display_name' => 'display_name_test',
            'name'         => 'name_test',
            'picture'      => 'picture_test',
            'profile'      => 'profile_test',
            'additional'   => [
                'add_test' => 'val'
            ]
        ];

        $model = new UserEntity($data);

        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($model->toArray()));
    }

    public function test_additional_attribute()
    {
        $data = [
            'id'           => 'id_test',
            'hash'         => 'id_test',
            'display_name' => 'display_name_test',
            'name'         => 'name_test',
            'picture'      => 'picture_test',
            'profile'      => 'profile_test',
            'additional'   => null,
        ];

        $model = new UserEntity($data);

        $this->assertJsonStringNotEqualsJsonString(json_encode($data), json_encode($model->toArray()));

        unset($data['additional']);
        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($model->toArray()));

        $data['additional'] = [
            'add_1' => 'test',
            'add_2' => 1,
            'add_3' => [
                'child' => true
            ]
        ];
        $model->additional  = $data['additional'];
        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($model->toArray()));
    }
}
