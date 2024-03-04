

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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // 外部キー制約、担当者で使用
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade'); // 外部キー制約、コースで使用
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade'); // 外部キー制約、オプションで使用
            $table->foreignId('merchandise_id')->nullable()->constrained('merchandises')->onDelete('cascade'); // 外部キー制約、物販で使用
            $table->foreignId('hairstyle_id')->nullable()->constrained('hairstyles')->onDelete('cascade'); // 外部キー制約、髪型で使用
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('cascade'); // 外部キー制約、予約表から受け取るで使用
            $table->string('name', 20)->nullable();// 顧客名
            $table->string('phone_number', 15)->nullable(); // 電話番号
            $table->text('features')->nullable(); // 特徴
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
        Schema::dropIfExists('customers');
    }
};



