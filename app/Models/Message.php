<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        "user_id_sender",
        "user_id_receiver",
        "title",
        "msg"
    ];

    /**
     * Devuelve el usuario que ha recibido el mensaje
     *
     * @return void
     */
    public function userReceived(){
        return $this->hasOne(User::class,'id','user_id_receiver');
    }

    /**
     * Devuelve el usuario que ha recibido el mensaje
     *
     * @return void
     */
    public function userSender(){
        return $this->hasOne(User::class,'id','user_id_sender');
    }
}
