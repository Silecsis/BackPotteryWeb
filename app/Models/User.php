<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    public function pieces(){
        return $this->hasMany(Piece::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    //Campos de búsqueda:
    public function scopeNombre($query, $nombre) {
    	if ($nombre) {
    		return $query->where('name','like',"%$nombre%");
    	}
    }

    public function scopeEmail($query, $email) {
    	if ($email) {
    		return $query->where('email','like',"%$email%");
    	}
    }

    public function scopeNick($query, $nick) {
    	if ($nick) {
    		return $query->where('nick','like',"%$nick%");
    	}
    }

    public function scopeFecha($query, $fecha) {
    	if ($fecha) {
    		return $query->whereDate('created_at','=',"$fecha");
    	}
    }

    public function scopeTipo($query, $tipo) {
    	if ($tipo && $tipo!=0) {
    		return $query->where('type','=',"$tipo");
    	}
    }

    public function scopeEmailExacto($query, $email) {
    	if ($email) {
    		return $query->where('email','=',"$email");
    	}
    }

    
}
