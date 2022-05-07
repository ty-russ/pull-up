<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('section')->default('A');;
            $table->string('rate_truck')->nullable();
            $table->string('rate_tuktuk')->nullable();
            $table->string('rate_car')->nullable();
            $table->string('rate_bike')->nullable();
            $table->string('rate_bus')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
};
