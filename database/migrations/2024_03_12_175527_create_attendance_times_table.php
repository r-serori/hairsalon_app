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
        Schema::create('attendance_times', function (Blueprint $table) {
            $table->id();
            $table->string('start_time', 30)->nullable();
            $table->string('end_time', 30)->nullable();
            $table->string('start_photo_path', 255)->nullable();
            $table->string('end_photo_path', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable(false);
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
        Schema::dropIfExists('attendance_times');
    }
};
