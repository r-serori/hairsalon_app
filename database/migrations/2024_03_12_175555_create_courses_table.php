<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Owner;


return new class extends Migration
{
    /**
     * マイグレーションを実行します。
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_name', 100)->nullable(false);
            $table->unsignedInteger('price')->nullable(false);
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * マイグレーションを元に戻します。
     *
     * @return void
     */
    public function down()
    {
        // courses テーブルを削除する場合
        Schema::dropIfExists('courses');
    }
};
