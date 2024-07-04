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
        Schema::create('options', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('option_name', 100)->nullable(false);
            $table->unsignedInteger('price')->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });

        // すべてのオーナーに '無し' オプションを挿入する
        $owners = \App\Models\Owner::all();
        foreach ($owners as $owner) {
            DB::table('options')->insert([
                'option_name' => '無し',
                'price' => 0, // 任意の価格を設定してください
                'owner_id' => $owner->id,
                'created_at' => now(),
                'updated_at' => now(),
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
        Schema::dropIfExists('options');
    }
};
