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
        Schema::create('customer_attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('customers_id');
            $table->unsignedBigInteger('attendances_id');
        
            $table->foreign('customers_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('attendances_id')->references('id')->on('attendances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_attendances');
    }
};
