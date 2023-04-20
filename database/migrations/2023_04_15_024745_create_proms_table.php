<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proms', function (Blueprint $table) {
            $table->id();
            $table->string('promo');
            $table->enum('jenis',['y','n'])->default('y');
            $table->enum('status',['y','n'])->default('y');
            $table->enum('istimewa',['y','n'])->default('n');
            $table->string('kuota');
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
        Schema::dropIfExists('proms');
    }
}
