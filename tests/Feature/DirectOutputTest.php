<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Models\CustomAttribute;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class CustomAttributesDirectOutputTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_a_single_direct_attribute()
    {        
        $user = User::first();
        
        $result = $user->attr('age')->set(61);
        $this->assertEquals(61, $user->attr('age')->get());
    }

    /** @test */
    public function it_can_retrieve_multiple_direct_attributes()
    {        
        $user = User::first();   
        $user->attr('favourite_colours')->set('blue');
        $user->attr('favourite_colours')->set('red');
        $user->attr('favourite_colours')->set('yellow');
        $result = $user->attr('favourite_colours')->direct()->get();

        $this->assertEquals(['blue', 'red', 'yellow'], $result);
    }
}