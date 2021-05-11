<?php

namespace App\Models;
/**
 * Modelo material_piece.
 * Se recogen la relación entre los modelos material y piece de la app.
 * Es la tabla intermedia de muchos a muchos.
 */

use Illuminate\Database\Eloquent\Model;

class material_piece extends Model
{
    public $timestamps = false;

    // public function scopeMaterialId($query, $materialId) {
    // 	if ($materialId) {
    // 		return $query->where('material_id','=',$materialId);
    // 	}
    // }
}
