<?php
/*Tabla ventas que será las ventas que se pueden realizar entre una pieza y la otra */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId('piece_id')->constrained()->onDelete('cascade')->onUpdate('cascade');//Foreign
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');//Foreign
            $table->decimal('price',$precision=8,$scale=2);
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
        Schema::dropIfExists('sales');
    }
}
