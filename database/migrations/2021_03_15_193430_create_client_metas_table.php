<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_metas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->unsigned()->index();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('want')->nullable();
            $table->string('interest')->nullable();
            $table->string('status')->nullable();
            $table->string('education')->nullable();
            $table->string('profession')->nullable();
            $table->integer('height')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('hair_color')->nullable();
            $table->longText('self_summary')->nullable();
            $table->longText('f_music')->nullable();
            $table->longText('f_shows')->nullable();
            $table->longText('f_movies')->nullable();
            $table->longText('f_books')->nullable();
            $table->string('religion')->nullable();
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
        Schema::dropIfExists('client_metas');
    }
}
