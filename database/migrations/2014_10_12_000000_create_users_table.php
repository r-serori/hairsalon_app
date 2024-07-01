<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Roles;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('name', 50)->nullable(false);
            $table->string('email', 200)->unique()->nullable(false);
            $table->string('phone_number', 20)->unique()->nullable(false);
            $table->string('password', 100)->nullable(false);
            $table->string('role', 50)->nullable(false);
            $table->boolean('isAttendance')->default(0)->nullable(false);
            // $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
