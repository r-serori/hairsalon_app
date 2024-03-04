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
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade')->onUpdate('cascade');// 外部キー制約
            $table->string('expense_name', 100);// 経費名
            $table->integer('price');// 金額
            $table->string('remarks', 100)->nullable();// 備考
            $table->date('expense_date')->nullable();// 経費発生日
            $table->string('expense_category', 50)->nullable();// 経費カテゴリー
            $table->decimal('tax', 8, 2)->default(1.10)->nullable();// 消費税
            $table->string('expense_location', 100)->nullable();// 経費発生場所
            $table->decimal('total_amount', 8, 2)->nullable();// 合計金額
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

