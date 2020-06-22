<?php

namespace PWRDK\CustomAttributes\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PWRDK\CustomAttributes\CustomAttributes;
use PWRDK\CustomAttributes\Tests\Models\User;
use PWRDK\CustomAttributes\CustomAttributesServiceProvider;
use PWRDK\CustomAttributes\Seeds\AttributeTypesTableSeeder;

class TestCase extends \Orchestra\Testbench\TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		$this->loadMigrationsFrom(__DIR__ . '/migrations');
		
		$this->artisan('migrate', ['--database' => 'testing']);
		
		(new AttributeTypesTableSeeder)->run();

		$this->createUsersTableAndAddNewUser();

        $this->createKeys();
	}

	protected function getPackageProviders($app)
	{
		return [
			CustomAttributesServiceProvider::class,
		];
	}

	protected function getEnvironmentSetUp($app)
	{
		// Setup default database to use sqlite :memory:
		$app['config']->set('database.default', 'testing');
		$app['config']->set('database.connections.testing', [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);
	}

	private function createKeys()
    {
        //- $handle, $name, $type, isUnique
        CustomAttributes::createKey('job_title', 'Job Title', 'text', true);
        CustomAttributes::createKey('date_of_birth', 'Job Title', 'datetime', true);
        CustomAttributes::createKey('age', 'Age', 'number', true);
        CustomAttributes::createKey('favourite_colours', 'Favourite Colours', 'text', false);
        CustomAttributes::createKey('contact_information', 'Contact Information', 'contact_information', true);
    }

    private function createUsersTableAndAddNewUser()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
        });

        \DB::table('users')->insert([
        	'first_name' => 'Rudolph',
        	'last_name' => 'Schwann',
        ]);
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        $attributeTypes = \DB::table('attribute_types')->get();
        $this->assertEquals(8, count($attributeTypes));
    }

}