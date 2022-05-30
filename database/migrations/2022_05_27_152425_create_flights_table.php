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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('code_departure');
            $table->string('code_arrival');
            $table->decimal('price', 6, 2);
            $table->timestamps();
        });
        
        Schema::table('flights', function (Blueprint $table) {
            $table->foreign('code_departure')->references('code')->on('airports');
            $table->foreign('code_arrival')->references('code')->on('airports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
};
