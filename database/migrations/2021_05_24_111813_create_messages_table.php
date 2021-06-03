<?php
/*Tabla mensajes que será los mensajes que se pueden enviar entre usuarios */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("msg");
            $table->boolean("read");
            $table->foreignId('user_id_receiver')->constrained()->onDelete('cascade')->onUpdate('cascade');//Foreign
            $table->foreignId('user_id_sender')->constrained()->onDelete('cascade')->onUpdate('cascade');//Foreign
            $table->boolean("delete_receiver")->default(false);;
            $table->boolean("delete_sender")->default(false);
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
        Schema::dropIfExists('messages');
    }
}
