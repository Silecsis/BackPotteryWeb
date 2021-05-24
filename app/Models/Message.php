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

    //---------------------------CAMPOS DE BUSQUEDA
    
    /**
     * Busca el mensaje por el campo user_id_received exacto
     *
     * @param [type] $query
     * @param [type] $userId
     * @return void
     */
    public function scopeUserIdReceived($query, $userId) {
    	if ($userId) {
    		return $query->where('user_id_receiver','=',$userId);
    	}
    }

    /**
     * Busca el mensaje por el campo user_id_sender exacto
     *
     * @param [type] $query
     * @param [type] $userId
     * @return void
     */
    public function scopeUserIdSender($query, $userId) {
    	if ($userId) {
    		return $query->where('user_id_sender','=',$userId);
    	}
    }

    /**
     * Busca un usuario por el campo name parecido (like).
     *
     * @param [type] $query
     * @param [type] $nombre
     * @return void
     */
    public function scopeTitle($query, $title) {
    	if ($title) {
    		return $query->where('title','like',"%$title%");
    	}
    }

    /**
     * Busca los mensajes por el campo read exacto
     *
     * @param [type] $query
     * @param [type] $toxico
     * @return void
     */
    public function scopeRead($query, $read) {
    	if ($read=='no') {
    		return $query->where('read','=',0);
    	}else if($read=='si'){
            return $query->where('read','=',1);
        }
    }

    /**
     * Busca los mensajes por el campo created_at exacto
     *
     * @param [type] $query
     * @param [type] $fecha
     * @return void
     */
    public function scopeFecha($query, $fecha) {
    	if ($fecha) {
    		return $query->whereDate('created_at','=',"$fecha");
    	}
    }
}
