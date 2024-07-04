<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandises', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('merchandise_name', 100)->nullable(false);
            $table->unsignedInteger('price')->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });

        // すべてのオーナーに '無し' 商品を挿入する
        $owners = \App\Models\Owner::all();
        foreach ($owners as $owner) {
            DB::table('merchandises')->insert([
                "merchandise_name" => "無し",
                "price" => 0,
                "owner_id" => $owner->id,
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchandises');
    }
};
