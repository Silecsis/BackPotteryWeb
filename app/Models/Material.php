<?php

namespace App\Models;

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
    public function pieces() {
        return $this->belongsToMany(Piece::class, "material_piece","material_id","piece_id");
    }

    //Campos de búsqueda:
    public function scopeNombre($query, $nombre) {
    	if ($nombre) {
    		return $query->where('name','like',"%$nombre%");
    	}
    }

    public function scopeTipo($query, $tipo) {
    	if ($tipo) {
    		return $query->where('type_material','like',"%$tipo%");
    	}
    }

    public function scopeTemperatura($query, $temperatura) {
    	if ($temperatura) {
    		return $query->where('temperature','=',"$temperatura");
    	}
    }

    public function scopeToxico($query, $toxico) {
    	if ($toxico=='no') {
    		return $query->where('toxic','=',0);
    	}else if($toxico=='si'){
            return $query->where('toxic','=',1);
        }
    }

    public function scopeFecha($query, $fecha) {
    	if ($fecha) {
    		return $query->whereDate('created_at','=',"$fecha");
    	}
    }

   
    use HasFactory;
}
