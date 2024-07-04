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
        Schema::create('owners', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('store_name', 100)->nullable(false);
            $table->string('postal_code', 10)->nullable(false); // 郵便番号
            $table->string('prefecture', 100)->nullable(false); //都道府県
            $table->string('city', 100)->nullable(false); // 市区町村
            $table->string('addressLine1', 200)->nullable(false); // 住所1
            $table->string('addressLine2', 200)->default('無し')->nullable(); // 住所2
            $table->string('phone_number', 20)->unique()->nullable(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable(false);
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
        Schema::dropIfExists('owners');
    }
};
