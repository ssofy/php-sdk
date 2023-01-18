<?php

use PHPUnit\Framework\TestCase;
use SSOfy\Models\Entities\ScopeEntity;

class ScopeModelTest extends TestCase
{
    public function test_mass_assign()
    {
        $data = [
            'id'          => 'id_test',
            'title'       => 'title_test',
            'description' => 'description_test',
            'icon'        => 'icon_test',
            'url'         => 'url_test',
        ];

        $model = new ScopeEntity($data);

        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($model->toArray()));
    }
}
