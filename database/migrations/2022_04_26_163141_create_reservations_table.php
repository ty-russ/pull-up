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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->timestamps();
            $table->timestamp('check_in')->useCurrent();
            $table->timestamp('check_out')->nullable();
            $table->string('reg_number_plate');
            $table->string('user_id');
            $table->string('vehicle_id');
            $table->string('rate')->nullable();
            $table->boolean('paid')->default(0);
            $table->string('section')->nullable();     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
