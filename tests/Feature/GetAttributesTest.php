<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\Tests\TestCase;

class GetAttributesTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_an_attribute()
    {        
        $user = User::first();
        
        $result = $user->attr('age')->set(61);

        $this->assertEquals(61, $user->attr('age')->get());
    }

    /** @test */
    public function it_can_retrieve_multiple_non_unique_attributes()
    {
        $user = User::first();
        
        $user->attr('favourite_colours')->set('blue');
        $user->attr('favourite_colours')->set('red');
        $user->attr('favourite_colours')->set('yellow');

        //- Same as Direct output
        $result = $user->attr('favourite_colours')->get()->pluck('output')->toArray();

        $this->assertEquals(['blue', 'red', 'yellow'], $result);
    }
}