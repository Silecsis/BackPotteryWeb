<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialPieceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_piece', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedBigInteger('piece_id');
            $table->unsignedBigInteger('material_id');
            $table->timestamps();

            //Foreign:
            $table->foreign('piece_id')->references('id')->on('pieces')->onDelete('cascade')->onUpdate('cascade');//Foreign
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade')->onUpdate('cascade');//Foreign
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_piece');
    }
}
