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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_name', 100)->nullable();
            $table->integer('price')->nullable();
            $table->date('date')->nullable();
            $table->string('expense_location',100)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('remarks',300)->nullable();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->nullable()->onDelete('cascade');
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
        Schema::dropIfExists('expenses');
    }
};
