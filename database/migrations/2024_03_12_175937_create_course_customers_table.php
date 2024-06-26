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
        Schema::create('course_customers', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade')->nullable(false);
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_customers');
    }
};
