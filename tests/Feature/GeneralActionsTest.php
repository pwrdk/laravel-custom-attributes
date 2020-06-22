<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class GeneralActionsTest extends TestCase
{
    /** @test */
    public function it_returns_false_when_no_attributes_attached()
    {
        $user = User::first();
        $allAttributes = $user->attr()->get();
        
        $this->assertEquals(false, $allAttributes);
    }

    /** @test */
    public function it_can_attach_a_new_attribute()
    {
        $user = User::first();
        
        $result = $user->attr('job_title')->set('Store Manager at Damernes Magasin');

        $this->assertInstanceOf(\PWRDK\CustomAttributes\Models\AttributeTypes\AttributeTypeDefault::class, $result);
    }
}