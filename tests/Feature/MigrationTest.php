<?php

namespace PWRDK\CustomAttributes\Tests;

use PWRDK\CustomAttributes\Tests\TestCase;

class MigrateDatabaseTest extends TestCase
{
    /** @test */
    public function it_runs_the_migrations()
    {
        $attributeTypes = \DB::table('attribute_types')->get();
        $this->assertEquals(8, count($attributeTypes));
    }
}