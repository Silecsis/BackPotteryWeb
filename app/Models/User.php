<?php

namespace App\Models;
/**
 * Modelo User.
 * Se recogen los usuarios de la app.
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'nick'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Metodo que llama a las piezas que tiene el usuario
     *
     * @return void
     */
    public function pieces(){
        return $this->hasMany(Piece::class);
    }

    /**
     * Metodo que llama a las ventas que tenga el usuario.
     *
     * @return void
     */
    public function sales(){
        return $this->hasMany(Sale::class);
    }

    /**
     * Metodo que llama a los mensajes recividos del usuario.
     *
     * @return void
     */
    public function messagesReceived(){
                            //Model, clave foranea, clave local en el model solicitado
        return $this->hasMany(Message::class,'user_id_receiver','id');
    }

    /**
     * Metodo que llama a los mensajes enviados del usuario.
     *
     * @return void
     */
    public function messagesSender(){
        return $this->hasMany(Message::class,'user_id_sender','id');
    }

    //Campos de búsqueda:

    /**
     * Busca un usuario por el campo name parecido (like).
     *
     * @param [type] $query
     * @param [type] $nombre
     * @return void
     */
    public function scopeNombre($query, $nombre) {
    	if ($nombre) {
    		return $query->where('name','like',"%$nombre%");
    	}
    }

    /**
     * Busca a un usuario por el campo email parecido (like)
     *
     * @param [type] $query
     * @param [type] $email
     * @return void
     */
    public function scopeEmail($query, $email) {
    	if ($email) {
    		return $query->where('email','like',"%$email%");
    	}
    }

    /**
     * Busca a un usuario por el campo nick parecido (like)
     *
     * @param [type] $query
     * @param [type] $nick
     * @return void
     */
    public function scopeNick($query, $nick) {
    	if ($nick) {
    		return $query->where('nick','like',"%$nick%");
    	}
    }

    /**
     * Busca a un usuario por el campo de created_at exacto
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

    /**
     * Busca a un usuario por el type exacto
     *
     * @param [type] $query
     * @param [type] $tipo
     * @return void
     */
    public function scopeTipo($query, $tipo) {
    	if ($tipo && $tipo!=0) {
    		return $query->where('type','=',"$tipo");
    	}
    }

    /**
     * Busca a un usuario por el email exacto.
     *
     * @param [type] $query
     * @param [type] $email
     * @return void
     */
    public function scopeEmailExacto($query, $email) {
    	if ($email) {
    		return $query->where('email','=',"$email");
    	}
    }

    
}
