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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->timestamps();
            $table->string('status')->default('pending');;
            $table->string('CheckoutRequestID');
            $table->string('MerchantRequestID');
            $table->string('Amount')->nullable();
            $table->string('MpesaReceiptNumber')->nullable();
            $table->string('TransactionDate')->nullable();
            $table->string('reg_number_plate');
            $table->string('reservation_id');
            $table->string('PaidBy')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
