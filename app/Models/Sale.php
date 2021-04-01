<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EloquentBuilder;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        "name",
        "piece_id",
        "user_id",
        "price"
    ];

    public function users(){
        return $this->hasOne(User::class,'user_id');
    }

    public function pieces(){
        return $this->hasOne(Piece::class,'piece_id');
    }

    use HasFactory;

    //Campos de búsqueda:
    public function scopeNombre($query, $nombre) {
    	if ($nombre) {
    		return $query->where('name','like',"%$nombre%");
    	}
    }

    public function scopeUserId($query, $userId) {
    	if ($userId) {
    		return $query->where('user_id','=',$userId);
    	}
    }

    public function scopePieceId($query, $pieceId) {
    	if ($pieceId) {
    		return $query->where('piece_id','=',$pieceId);
    	}
    }

    public function scopePrecio($query, $precio) {
    	if ($precio) {
    		return $query->where('price','=',$precio);
    	}
    }

    public function scopeFecha($query, $fecha) {
    	if ($fecha) {
    		return $query->whereDate('created_at','=',"$fecha");
    	}
    }
}
