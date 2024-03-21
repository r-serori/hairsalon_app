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
        Schema::create('hairstyle_schedules', function (Blueprint $table) {
            $table->foreignId('hairstyles_id')->nullable()->constrained('hairstyles')->onDelete('cascade');
            $table->foreignId('schedules_id')->nullable()->constrained('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hairstyle_schedules');
    }
};
