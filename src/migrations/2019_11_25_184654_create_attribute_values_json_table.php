<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeValuesJsonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values_json', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_attribute_id');
            $table->json('value');
            $table->timestamps();

            $table->foreign('custom_attribute_id')->references('id')->on('custom_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_values_json');
    }
}
