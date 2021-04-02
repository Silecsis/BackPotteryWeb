<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Controlador del modelo Material.
 * Contiene el CRUD de Material.
 * Socios solo pueden ver.
 */
class MaterialController extends Controller
{
     /**
     * Lista todos los usuarios
     *
     * @return void
     */
    public function all()
    {
        $materials=Material::all();

        return response()->json($materials);
    }

    /**
     * Devuelve un usuario localizado por el id.
     *
     */
    public function show($id)
    {
        $materials = Material::find($id);
 
        if (!$materials) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $materials->toArray()
        ], 200);
    }

    /**
     * Edita el usuario
     *
     */
    public function update(Request $request, $id)
    {
        $material = Material::find($id);
 
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 400);
        }
 
        $updated = $material->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'El material no puede ser actualizado'
            ], 500);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type_material' => 'required',
            'temperature' => 'required',
            'toxic' => 'required',
         ]);
 
        $material = new Material();
        $material->name = $request->name;
        $material->type_material = $request->type_material;
        $material->temperature = $request->temperature;
        $material->toxic = $request->toxic;
        $material->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $material->updated_at = Carbon::now()->format('Y-m-d H:i:s');
 
        if ($material->save())
            return response()->json([
                'success' => true,
                'data' => $material->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Se ha producido un error a la hora de crear el material'
            ], 500);
    }

    /**
     * Elimina a un usuario de la bbdd.
     */
    public function destroy($id)
    {
        $material = Material::find($id);
 
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 400);
        }
 
        if ($material->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'El material '.$material->name.' ha sido eliminado correctamente',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El material no puede ser eliminado'
            ], 500);
        }
    }
}
