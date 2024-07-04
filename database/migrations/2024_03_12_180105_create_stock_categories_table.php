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
        Schema::create('stock_categories', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('category', 100)->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
            $table->timestamps();
        });

        // すべてのオーナーに '無し' カテゴリを挿入する
        $owners = \App\Models\Owner::all();
        foreach ($owners as $owner) {
            DB::table('stock_categories')->insert([
                'category' => '無し',
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
        Schema::dropIfExists('stock_categories');
    }
};
