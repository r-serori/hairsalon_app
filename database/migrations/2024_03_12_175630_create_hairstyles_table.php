<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Owner;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hairstyles', function (Blueprint $table) {
            $table->id();
            $table->string('hairstyle_name', 100)->nullable(false);
            $table->foreignId('owner_id')->constrained()->onDelete('cascade')->nullable(false);
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
        Schema::dropIfExists('hairstyles');
    }
};
