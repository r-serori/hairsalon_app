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
        Schema::create('merchandise_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchandises_id')->constrained('merchandises')->onDelete('cascade');
            $table->foreignId('schedules_id')->constrained('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchandise_schedules');
    }
};