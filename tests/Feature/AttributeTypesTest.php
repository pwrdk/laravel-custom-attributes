<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class AttributeTypesTest extends TestCase
{
    /** @test */
    public function it_returns_a_carbon_instance_for_a_date_type()
    {
        $user = User::first();
        $user->attr('date_of_birth')->set('1897-11-09');

        $attributeValue = $user->attr('date_of_birth')->get();

        $this->assertInstanceOf(\Carbon\Carbon::class, $attributeValue);
    }

    /** @test */
    public function it_returns_a_string_for_default_type()
    {
        $user = User::first();
        $user->attr('job_title')->set('Store Manager at Damernes Magasin');

        $attributeValue = $user->attr('job_title')->get();

        $this->assertTrue(is_string($attributeValue), "Do!");
    }
}