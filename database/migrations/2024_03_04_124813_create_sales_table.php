
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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // 外部キー制約、担当者で使用
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade'); // 外部キー制約、コース料金で使用
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade'); // 外部キー制約、オプション料金で使用
            $table->foreignId('merchandise_id')->nullable()->constrained('merchandises')->onDelete('cascade'); // 外部キー制約、物販料金で使用
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade'); // 外部キー制約、顧客名で使用
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('cascade'); // 外部キー制約、予約表から受け取るで使用
            $table->decimal('tax', 8, 2)->default(1.10)->nullable();// 消費税
            $table->integer('total_price')->nullable();// 合計金額
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
        Schema::dropIfExists('sales');
    }
};

