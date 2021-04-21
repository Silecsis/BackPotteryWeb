<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Piece;
use App\Models\User;
use App\Models\material_piece;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * CRUD del modelo piezas.
 * Los admin tendrán acceso al listado de todas las piezas del sistema.
 * Los usuarios solo verán una lista de las piezas que poseen.
 * Los usuarios podran editar, eliminar sus piezas y crear una nueva pieza.
 */
class PieceController extends Controller
{

    public function all(Request $request)
    {
        //Solo recoge los usuarios que tienen, al menos, una pieza
        $users=User::whereHas('pieces')->get();

        //Tipos de filtrado:
        $nombre= $request->get('buscaNombre');
        $idUser= $request->get('buscaUser');
        $vendido= $request->get('buscaVendido');
        $fecha= $request->get('buscaFechaLogin');

         //La pieza con los filtros:
         $pieces = Piece::nombre($nombre)->userId($idUser)->vendido($vendido)->fecha($fecha)->get();

         foreach($users as $user){
            foreach($pieces as $piece){
                if($user->id == $piece->user_id){
                    $piece->emailUser=$user->email;
                };
            }
        }

        $response=[
            "users"=>$users,
            "pieces"=> $pieces,
        ];


        return response()->json($response);
    }
    
    /**
     * Devuelve un usuario localizado por el id.
     *
     */
    public function show($id)
    {
        if(Auth::user()->type=='admin'){
            $piece = Piece::find($id);
            
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada '
                ], 400);
            }
        
            return response()->json([
                'success' => true,
                'data' => $piece->toArray()
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Edita el usuario
     *
     */
    public function update(Request $request, $id)
    {
        $piece = Piece::find($id);
 
        if (!$piece) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 400);
        }
 
        $updated = $piece->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'La pieza no puede ser actualizada'
            ], 500);
    }

    /**
     * Elimina a un usuario de la bbdd.
     */
    public function destroy($id)
    {
        $piece = Piece::find($id);
 
        if (!$piece) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 400);
        }
 
        if ($piece->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'La pieza '.$piece->name.' ha sido eliminada correctamente',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'La pieza no puede ser eliminada'
            ], 500);
        }
    }

    //----------------------------PARA IAMGENES-------------------
     /**
    * Devuelve la imagen avatar del usuario
    *
    * @param [type] $filename
    * @return void
    */
    public function getImage($filename){     
        $file = Storage::disk('pieces')->get($filename);
        return new Response($file, 200);
     }
}
