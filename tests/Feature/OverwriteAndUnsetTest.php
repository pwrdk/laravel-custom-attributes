<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Models\CustomAttribute;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class OverwriteAndUnsetTest extends TestCase
{
    /** @test */
    public function it_can_unset_an_attribute()
    {
        $user = User::first();
        $result = $user->attr('job_title')->set('Store Manager at Damernes Magasin');
        
        $this->assertEquals('Store Manager at Damernes Magasin', $user->attr('job_title')->get());

        $user->attr('job_title')->unset();
        $this->assertFalse($user->attr('job_title')->get());
    }

    /** @test */
    public function it_can_remove_a_non_unique_attribute_value()
    {
        $user = User::first();
        $user->attr('favourite_colours')->set('blue');
        $user->attr('favourite_colours')->set('red');
        $user->attr('favourite_colours')->set('yellow');

        $result = $user->attr('favourite_colours')->get()->pluck('output')->toArray();

        $this->assertEquals(['blue', 'red', 'yellow'], $result);

        //- Direct access to the underlying model
        $attributeValue = $user->attr('favourite_colours')->get()->where('output', 'red')->first();
        
        CustomAttribute::find($attributeValue->id)->delete();

        $result = $user->attr('favourite_colours')->get()->pluck('output')->toArray();

        $this->assertEquals(['blue', 'yellow'], $result);
    }
}