<?php

namespace App\Models;

/**
 * Modelo Piece.
 * Se recogen las piezas de la app.
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    protected $table = 'pieces';

    protected $fillable = [
        "name",
        "user_id",
        "description",
        "sold",
        "total_materials"
    ];

    //La relacion de muchos

    /**
     * Recoge los materiales que contiene la pieza
     *
     * @return void
     */
    public function materials() {
        return $this->belongsToMany(Material::class, "material_piece","piece_id","material_id");
    }

    /**
     * Recoge el usuario al que pertenece la pieza
     *
     * @return void
     */
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Recoge la venta a la que pertenece la pieza 
     *
     * @return void
     */
    public function sales(){
        return $this->belongsTo(Sale::class);
    }

    use HasFactory;

    //Campos de búsqueda:
    /**
     * Busca a la pieza mediante el campo name parecido (like)
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
     * Busca la pieza por el campo user_id exacto
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
     * Busca la pieza por el campo created_at exacto
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
     * Busca la pieza por el campo toxic exacto
     *
     * @param [type] $query
     * @param [type] $toxico
     * @return void
     */
    public function scopeToxico($query, $toxico) {
    	if ($toxico=='no') {
    		return $query->where('toxic','=',0);
    	}else if($toxico=='si'){
            return $query->where('toxic','=',1);
        }
    }

    /**
     * Busca la pieza por el campo sold exacto
     *
     * @param [type] $query
     * @param [type] $vendido
     * @return void
     */
    public function scopeVendido($query, $vendido) {
        if ($vendido=='no') {
    		return $query->where('sold','=',0);
    	}else if($vendido=='si'){
            return $query->where('sold','=',1);
        }
    }
}
