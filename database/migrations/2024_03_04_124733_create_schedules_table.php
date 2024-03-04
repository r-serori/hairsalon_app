
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // 外部キー制約、担当者で使用
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade'); // 外部キー制約、コースで使用
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade'); // 外部キー制約、オプションで使用
            $table->foreignId('merchandise_id')->nullable()->constrained('merchandises')->onDelete('cascade'); // 外部キー制約、物販で使用
            $table->string('phone_number', 15)->nullable(); // 電話番号
            $table->string('name', 20)->nullable(); // 名前
            $table->text('features')->nullable(); // 特徴
            $table->date('reservation_date')->nullable(); // 予約日
            $table->time('reservation_start_time')->nullable(); // 予約開始時間
            $table->time('reservation_end_time')->nullable(); // 予約終了時間
            $table->integer('price')->nullable(); // 合計金額
            $table->boolean('new_customer')->default(true)->nullable();// 新規顧客, true: 新規顧客, false: 既存顧客
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
        Schema::dropIfExists('schedules');
    }
};



