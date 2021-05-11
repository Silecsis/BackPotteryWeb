<?php

namespace App\Models;

/**
 * Modelo Material.
 * Se recogen los materiales de la app.
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        "name",
        "type_material",
        "temperature",
        "toxic"
    ];


    //La relacion de muchos
    /**
     * Devuelve las piezas en las que se ha aplicado el material
     *
     * @return void
     */
    public function pieces() {
        return $this->belongsToMany(Piece::class, "material_piece","material_id","piece_id");
    }

    //Campos de búsqueda:
    /**
     * Busca el material por el campo name parecido (like)
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
     * Busca el material por el campo type_material parecido (like)
     *
     * @param [type] $query
     * @param [type] $tipo
     * @return void
     */
    public function scopeTipo($query, $tipo) {
    	if ($tipo) {
    		return $query->where('type_material','like',"%$tipo%");
    	}
    }

    /**
     * Busca el material por el campo temperature exacto
     *
     * @param [type] $query
     * @param [type] $temperatura
     * @return void
     */
    public function scopeTemperatura($query, $temperatura) {
    	if ($temperatura) {
    		return $query->where('temperature','=',"$temperatura");
    	}
    }

    /**
     * Busca el material por el campo toxic exacto
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
     * Busca el material por el campo created:at exacto
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

   
    use HasFactory;
}
