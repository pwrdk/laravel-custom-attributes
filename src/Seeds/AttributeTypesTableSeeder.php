<?php

namespace PWRDK\CustomAttributes\Seeds;

use Illuminate\Database\Seeder;

class AttributeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('attribute_types')->insert([
            'handle' => 'text',
            'display_name' => 'Just regular text'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'number',
            'display_name' => 'Number/Integer'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'boolean',
            'display_name' => 'Boolean On/Off'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'contact_information',
            'display_name' => 'Contact Information'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'datetime',
            'display_name' => 'Date Time'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'file',
            'display_name' => 'File'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'json',
            'display_name' => 'JSON'
        ]);

        \DB::table('attribute_types')->insert([
            'handle' => 'email',
            'display_name' => 'Email'
        ]);
    }
}
