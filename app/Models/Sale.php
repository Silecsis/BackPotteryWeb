<?php

namespace App\Models;
/**
 * Modelo Sale.
 * Se recogen loas ventas realizadas de la app.
 */

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

    /**
     * Devuelve el usuario que ha realizado la venta
     *
     * @return void
     */
    public function users(){
        return $this->hasOne(User::class,'user_id');
    }

    /**
     * Devuelve la pieza de la que se ha realizado la venta
     *
     * @return void
     */
    public function pieces(){
        return $this->hasOne(Piece::class,'piece_id');
    }

    use HasFactory;

    //Campos de búsqueda:
    /**
     * Busca la venta por el campo name parecido (like)
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
     * Busca la venta por el user_id exacto
     *
     * @param [type] $query
     * @param [type] $userId
     * @return void
     */
    public function scopeUserId($query, $userId) {
    	if ($userId) {
    		return $query->where('user_id','=',$userId);
    	}
    }

    /**
     * Busca la venta por el campo piece_id exacto
     *
     * @param [type] $query
     * @param [type] $pieceId
     * @return void
     */
    public function scopePieceId($query, $pieceId) {
    	if ($pieceId) {
    		return $query->where('piece_id','=',$pieceId);
    	}
    }

    /**
     * Busca la venta por el campo price exacto
     *
     * @param [type] $query
     * @param [type] $precio
     * @return void
     */
    public function scopePrecio($query, $precio) {
    	if ($precio) {
    		return $query->where('price','=',$precio);
    	}
    }

    /**
     * Busca la venta por el campo created_at exacto
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
