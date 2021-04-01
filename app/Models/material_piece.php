<?php

namespace App\Models;

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
