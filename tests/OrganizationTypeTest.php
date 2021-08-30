<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OrganizationTypeTest extends TestCase
{
    use  DatabaseTransactions;

    const ROUTE_PREFIX = "api.v1.organization-types.";

    /** OrganizationType Create TestCase */
    public function testCanCreateTask()
    {

        $formData = [
            "title_en" => "Test OrganizationType EN",
            "title_bn" => "Test OrganizationType Bn",
            "is_government" => 1
        ];
        $this->post(route($this::ROUTE_PREFIX."store"), $formData)
            ->seeStatusCode(200);

    }

}
