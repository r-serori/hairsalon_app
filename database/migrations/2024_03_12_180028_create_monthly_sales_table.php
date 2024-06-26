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
        Schema::create('monthly_sales', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('year_month', 20)->nullable(false);
            $table->unsignedInteger('monthly_sales')->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_sales');
    }
};
