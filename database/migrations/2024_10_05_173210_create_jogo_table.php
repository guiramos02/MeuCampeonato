<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJogoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jogo', function (Blueprint $table) {
            $table->id();
            $table->integer('time_casa');
            $table->integer('time_fora');

            $table->foreign('time_casa')
                ->references('id')
                ->on('times')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('time_fora')
                ->references('id')
                ->on('times')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->integer('gols_time_casa')->nullable();
            $table->integer('gols_time_fora')->nullable();
            $table->string('fase',50);
            $table->dateTime('data_inicio_campeonato');
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
        Schema::dropIfExists('jogo');
    }
}
