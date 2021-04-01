<?php

namespace App\Models;

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
    public function materials() {
        return $this->belongsToMany(Material::class, "material_piece","piece_id","material_id");
    }

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function sales(){
        return $this->belongsTo(Sale::class);
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

    public function scopeFecha($query, $fecha) {
    	if ($fecha) {
    		return $query->whereDate('created_at','=',"$fecha");
    	}
    }

    public function scopeToxico($query, $toxico) {
    	if ($toxico=='no') {
    		return $query->where('toxic','=',0);
    	}else if($toxico=='si'){
            return $query->where('toxic','=',1);
        }
    }

    public function scopeVendido($query, $vendido) {
        if ($vendido=='no') {
    		return $query->where('sold','=',0);
    	}else if($vendido=='si'){
            return $query->where('sold','=',1);
        }
    }
}
