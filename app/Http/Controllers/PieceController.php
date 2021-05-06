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
use Illuminate\Support\Carbon;

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
        if(Auth::user()->type=='admin'){
            $piece = Piece::find($id);
            
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada'
                ], 400);
            }

            //Si existe la pieza, primero validamos.   
            $request->validate([
                'name' => 'required|string|max:255|min:6|unique:users,email,' . $piece->id,
                'description' => 'required|string|',
                'sold' => 'required|boolean|max:10|in:0,1',
             ]); 


            $piece->fill($request->all());
            $piece->updated_at = Carbon::now()->format('Y-m-d H:i:s');

             //Subir la imagen
            $image= $request->file('image'); 
            // Si recibimos un objeto imagen tendremos que utilizar el disco para almacenarla
            // Para ello utilizaremos un objeto storage de Laravel
            if($image){
                // Generamos un nombre único para la imagen basado en time() y el nombre original de la imagen
                $image_name =  time() . $image->getClientOriginalName();
                $image_delete= $piece->img;//Será para borrar la imagen y no saturar la carpeta
                
                // Seleccionamos el disco virtual users, extraemos el fichero de la carpeta temporal
                // donde se almacenó y guardamos la imagen recibida con el nombre generado
                Storage::disk('pieces')->put($image_name, File::get($image));

                //Si no es la imagen por defecto, eliminamos la que tenia antes
                if($image_delete != 'default-img.png'){
                    Storage::disk('pieces')->delete($image_delete);
                }
                $piece->img = $image_name; 
            }
        
            $updated= $piece->save();

        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'La pieza no puede ser actualizada'
                ], 500);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina a un usuario de la bbdd.
     */
    public function destroy($id)
    {
        if(Auth::user()->type=='admin'){
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
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function detail($id)
    {
        $piece = Piece::find($id);

        if (!$piece) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 400);
        }

        $user=User::find($piece->user_id);

        $response=[
            "piece"=> $piece,
            "user"=>$user,
            "materials"=>$piece->materials,
        ];
        
        return response()->json($response);

    }

    /**
     * Devuelve un usuario localizado por el id.
     *
     */
    public function addSale($id)
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
     * Devuelve un usuario localizado por el id.
     *
     */
    public function changeSold($id)
    {
        if(Auth::user()->type=='admin'){
            $piece = Piece::find($id);
    
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada '
                ], 400);
            }

            if($piece->sold){
                $piece->sold = false;
                $piece->save();
            }else{
                $piece->sold = true;
                $piece->save();
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Campo sold de pieza actualizado '
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    } 


    /*-------------------------------MYPIECES--------------------------*/
    
    public function allMyPieces($id,Request $request)
    {
        //Recogemos el user pasado por id.
        $user=User::find($id);

        if(Auth::user()->id==$user->id){

            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $vendido= $request->get('buscaVendido');
            $fecha= $request->get('buscaFechaLogin');

             //La pieza con los filtros:
             $pieces = Piece::userId($id)->nombre($nombre)->vendido($vendido)->fecha($fecha)->get();

            $response=[
                "pieces"=> $pieces,
            ];


            return response()->json($response);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina a un usuario de la bbdd.
     */
    public function destroyMyPieces($idUser, $id)
    {
        if(Auth::user()->id==$idUser){
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
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

     /**
     * Devuelve un usuario localizado por el id.
     *
     */
    public function showMyPieces($idUser,$id)
    {
        if(Auth::user()->id==$idUser){
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
    public function updateMyPieces($idUser,Request $request, $id)
    {
        if(Auth::user()->id==$idUser){
            $piece = Piece::find($id);
            
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada'
                ], 400);
            }

            //Si existe la pieza, primero validamos.   
            $request->validate([
                'name' => 'required|string|max:255|min:6|unique:users,email,' . $piece->id,
                'description' => 'required|string|',
                'sold' => 'required|boolean|max:10|in:0,1',
             ]); 


            $piece->fill($request->all());
            $piece->updated_at = Carbon::now()->format('Y-m-d H:i:s');

             //Subir la imagen
            $image= $request->file('image'); 
            // Si recibimos un objeto imagen tendremos que utilizar el disco para almacenarla
            // Para ello utilizaremos un objeto storage de Laravel
            if($image){
                // Generamos un nombre único para la imagen basado en time() y el nombre original de la imagen
                $image_name =  time() . $image->getClientOriginalName();
                $image_delete= $piece->img;//Será para borrar la imagen y no saturar la carpeta
                
                // Seleccionamos el disco virtual users, extraemos el fichero de la carpeta temporal
                // donde se almacenó y guardamos la imagen recibida con el nombre generado
                Storage::disk('pieces')->put($image_name, File::get($image));

                //Si no es la imagen por defecto, eliminamos la que tenia antes
                if($image_delete != 'default-img.png'){
                    Storage::disk('pieces')->delete($image_delete);
                }
                $piece->img = $image_name; 
            }
        
            $updated= $piece->save();

        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'La pieza no puede ser actualizada'
                ], 500);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Manda a la vista.
     *
     * @return \Illuminate\Http\Response
     */
    public function newMyPiece($idUser)
    {
        if(Auth::user()->id==$idUser){
            $materials=Material::all();

            if (!$materials) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existen materiales'
                ], 400);
            }
        
            return response()->json([
                'success' => true,
                'data' => $materials->toArray()
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
      
    }

    /**
     * Crea una nueva pieza.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMyPiece($idUser,Request $request)
    {   
        if(Auth::user()->id==$idUser){
            $piece = new Piece;
            
             //Primero validamos.   
             $request->validate([
               'name' => 'required|string|max:255|min:6|unique:users,email,',
               'description' => 'required|string|',
               'image'=>'required',
               'materialsArr'=>'required|array|min:1',
            ]); 

              //Subir la imagen
              $image= $request->file('image'); 
              // Si recibimos un objeto imagen tendremos que utilizar el disco para almacenarla
              // Para ello utilizaremos un objeto storage de Laravel
              if($image){
                  // Generamos un nombre único para la imagen basado en time() y el nombre original de la imagen
                  $image_name =  time() . $image->getClientOriginalName();

                  // Seleccionamos el disco virtual users, extraemos el fichero de la carpeta temporal
                  // donde se almacenó y guardamos la imagen recibida con el nombre generado
                  Storage::disk('pieces')->put($image_name, File::get($image));

                  $piece->img = $image_name;   
              } 

              $piece->name = $request->name;
              $piece->description = $request->description;
              $piece->created_at = Carbon::now()->format('Y-m-d H:i:s');
              $piece->updated_at = Carbon::now()->format('Y-m-d H:i:s');

              //Por defecto, la pieza no estará vendida y tendrá el id del usuario logueado.
              $piece->user_id = Auth::user()->id;
              $piece->sold = 0;


              $materials=$request->materialsArr;

              //Si materiales no está vacio.
              if ($materials!=null && $piece->save()) { 

                     //Le asignamos los materiales a la pieza.
                     foreach($materials as $idMaterial){
                        $piece->materials()->attach($idMaterial);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'data' => $piece->toArray()
                    ]);

              } else {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Se ha producido un error a la hora de crear el material'
                ], 500);
              }
            
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
      
    }




    //----------------------------PARA IMAGENES-------------------
     /**
    * Devuelve la imagen de la pieza
    *
    * @param [type] $filename
    * @return void
    */

     public function getImage($id){  
        $piece=Piece::find($id);
        
        if($piece){

            if($piece->img != null && Storage::disk('pieces')->exists($piece->img)){
                $filename=$piece->img;  
                $file = Storage::disk('pieces')->get($filename);
                return new Response($file, 200);
            }             
        }

        return new Response(null,404);
    }
}
