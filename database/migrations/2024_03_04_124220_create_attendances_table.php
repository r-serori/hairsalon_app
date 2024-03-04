
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // 外部キー制約
            $table->string('name', 20);// 名前
            $table->string('position', 10)->nullable();// 役職
            $table->string('phone_number', 15)->nullable();// 電話番号
            $table->time('start_time')->nullable();// 出勤時間
            $table->time('end_time')->nullable();// 退勤時間
            $table->time('break_time')->nullable();// 休憩時間
            $table->string('address')->nullable();// 住所
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
        Schema::dropIfExists('attendances');
    }
};



