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
            $table->string('product_name', 100);// 商品名
            $table->string('category',50)->nullable();// カテゴリー
            $table->integer('quantity');// 数量
            $table->string('remarks', 100)->nullable();// 備考
            $table->integer('purchase_price');// 仕入れ価格、小数点以下2桁まで、合計10桁まで、
            $table->string('supplier',50)->nullable();// 仕入先
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


