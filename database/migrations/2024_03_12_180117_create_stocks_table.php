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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('product_name'); //商品名
            $table->integer('product_price'); //商品価格 
            $table->integer('quantity'); //数量
            $table->string('remarks')->nullable(); //備考
            $table->string('supplier')->nullable(); //仕入先
            $table->integer('notice');
            $table->foreignId('stock_category_id')->constrained('stock_categories')->onDelete('cascade');
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
        Schema::dropIfExists('stocks');
    }
};
