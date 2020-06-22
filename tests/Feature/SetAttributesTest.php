<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class SetAttributesTest extends TestCase
{
    /** @test */
    public function it_can_overwrite_a_unique_attribute()
    {
        $user = User::first();

        $result = $user->attr('age')->set(61);

        $this->assertEquals(61, $user->attr('age')->get());
        
        $result = $user->attr('age')->set(62);

        $this->assertEquals(62, $user->attr('age')->get());
    }

    /** @test */
    public function it_can_add_multiple_non_unique_attributes()
    {
        $user = User::first();
        
        $user->attr('favourite_colours')->set('blue');
        $user->attr('favourite_colours')->set('red');
        $user->attr('favourite_colours')->set('yellow');
        
        $this->assertEquals(3, $user->attr('favourite_colours')->get()->count());
    }
}