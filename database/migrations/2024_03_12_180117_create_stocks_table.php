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
            $table->id()->unique();
            $table->string('product_name', 100)->nullable(false); //商品名
            $table->unsignedInteger('product_price')->nullable(false); //商品価格 
            $table->unsignedInteger('quantity')->default(0)->nullable(false); //数量
            $table->string('remarks', 150)->default('無し')->nullable(); //備考
            $table->string('supplier', 100)->default('無し')->nullable(); //仕入先
            $table->unsignedInteger('notice')->default(0)->nullable(false); //通知
            $table->foreignId('stock_category_id')->default(1)->constrained('stock_categories')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);;
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
