<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


/**
 * CRUD del modelo sale (venta). 
 * 
 * Las ventas se borran automáticamente cuando se borra la pieza o 
 * cuando se cambie el estado de venta de la pieza.
 * 
 * La lectura de las ventas realizadas podrá hacerla cualquier usuario.
 * 
 * El CRUD absoluto de todas las ventas del sistema solo podrá realizarlo los 
 * usuarios con rol administrador.
 * 
 * Los usuarios logados podrán hacer el CRUD completo sobre sus propias ventas.
 */
class SaleController extends Controller
{
    /**
     * Lista todas las ventas.
     * Filtra las ventas.
     * Puede acceder cualquier usuario.
     */
    public function all(Request $request)
    {
        //Recogemos solo los usuarios que tienen al menos 1 venta
        //con el whereHas llamando a la relacion de ventas.
        $users=User::whereHas('sales')->get();

        //Solo seleccionamos las piezas que estén vendidas
        $pieces=Piece::vendido("si")->get();

        //Tipos de filtrado:
        $nombre= $request->get('buscaNombre');
        $idUser= $request->get('buscaUser');
        $idPiece= $request->get('buscaPiece');
        $fecha= $request->get('buscaFechaLogin');
        $precio= $request->get('buscaPrecio');


        $sales=Sale::nombre($nombre)->userId($idUser)->pieceId($idPiece)->fecha($fecha)->precio($precio)->get();

        foreach($users as $user){
            foreach($sales as $sale){
                if($user->id == $sale->user_id){
                    $sale->emailUser=$user->email;
                };
            }
        }

        foreach($pieces as $piece){
            foreach($sales as $sale){
                if($piece->id == $sale->piece_id){
                    $sale->namePiece=$piece->name;
                };
            }
        }
        
        $response=[
            "users"=>$users,
            "pieces"=>$pieces,
            "sales"=>$sales,
        ];


        return response()->json($response);
    } 
    
    /**
     * Devuelve una venta localizada por el id desde la lista de "Ventas Realizadas".
     * Será devuelta en la vista de edición de venta.
     * Solo puede acceder el tipo administrador.
     */
    public function show($id)
    {
        if(Auth::user()->type=='admin'){
            $sale = Sale::find($id);
    
            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada '
                ], 400);
            }
    
            return response()->json([
                'success' => true,
                'data' => $sale->toArray()
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    } 

     /**
     * Edita la venta desde la lista de "Ventas Realizadas".
     * Solo puede acceder el tipo administrador.
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->type=='admin'){
            $sale = Sale::find($id);
            
            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 400);
            }

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
                'name' => 'required|string|min:3|max:20|unique:sales,name,' . $sale->id,
             ]); 
            
        
            $updated = $sale->fill($request->all())->save();
        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'La venta no puede ser actualizada'
                ], 500);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina a una venta al modificar el estado de venta de una pieza o
     * al eliminar dicha pieza.
     * Solo puede acceder el tipo administrador.
     */
    public function destroy($id)
    {
        if(Auth::user()->type=='admin'){
            $piece = Piece::find($id);
 
            //Sino existe la pieza o existe pero no está vendida
            if (!$piece || !$piece->sold) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 400);
            }

            $sale = Sale::pieceId($id);
            $piece->sold=false;
                
            if ($sale->delete()) {  

                if($piece->save()){
                    return response()->json([
                        'success' => true,
                        'message' => 'La pieza '.$piece->name.' se ha actualizado correctamente a no vendida',
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Se ha creado la venta pero ha habido un error con la actualización del estado de venta de la pieza '.$piece->name.".",
                    ], 400);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'La pieza no ha podido ser actualizada'
                ], 500);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La venta no puede ser eliminada'
                ], 500);
            }
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }


    /**
     * Crea una nueva venta al modificar el estado de venta de una pieza. 
     * Solo puede acceder el tipo administrador.
     */
    public function create($idPiece,Request $request)
    {
        if(Auth::user()->type=='admin'){
            $piece = Piece::find($idPiece);
            
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada'
                ], 400);
            }

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',//Valida decimal
                'name' => 'required|string'
             ]); 
            
            $sale = new Sale(); 
            $sale->name = $request->name;
            $sale->price = $request->price;
            $sale->user_id = $piece->user_id;
            $sale->piece_id = $piece->id;
            $sale->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $sale->updated_at = Carbon::now()->format('Y-m-d H:i:s');

            
            $piece->sold=true;

            if ($sale->save()) { 
                 if($piece->save()){
                    return response()->json([
                        'success' => true,
                        'message' => 'La pieza '.$piece->name.' se ha actualizado correctamente a no vendida',
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Se ha creado la venta pero ha habido un error con la actualización del estado de venta de la pieza '.$piece->name.".",
                    ], 400);
                }
               
                return response()->json([
                    'success' => true,
                    'message' => 'La venta '.$sale->name.' ha sido creada correctamente',
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

    /*-------------------------------MYSALES--------------------------*/

    /**
     * Lista todas las ventas del usuario logado.
     * Filtra las ventas.
     * Puede acceder cualquier usuario logado.
     */
    public function allMySales($id,Request $request)
    {
        $pieces=Piece::all();
        $user=User::find($id);


        if(Auth::user()->id==$user->id){

            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $idPiece= $request->get('buscaPiece');
            $fecha= $request->get('buscaFechaLogin');
            $precio= $request->get('buscaPrecio');


            $sales=Sale::userId($user->id)->nombre($nombre)->pieceId($idPiece)->fecha($fecha)->precio($precio)->get();

            foreach($pieces as $piece){
                foreach($sales as $sale){
                    if($piece->id == $sale->piece_id){
                        $sale->namePiece=$piece->name;
                    };
                }
            }


            return response()->json($sales);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Devuelve una venta localizada por el id desde la lista de "Mis ventas Realizadas".
     * Será devuelta en la vista de edición de mi venta.
     * Puede acceder cualquier usuario logado y solo tendrá acceso a sus ventas.
     */
    public function showMySale($idUser,$id)
    {
        $sale = Sale::find($id);
        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada '
            ], 400);
        }

        if(Auth::user()->id==$idUser){
            
            $piece = Piece::find($sale->piece_id);
            return response()->json([
                'success' => true,
                'data' => $sale->toArray(),
                'piece' => $piece->toArray(),
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

     /**
     * Edita la venta desde la lista de "Mis ventas Realizadas".
     * Puede acceder cualquier usuario logado y solo tendrá acceso a sus ventas.
     */
    public function updateMySale($idUser,$id,Request $request)
    {
        $sale = Sale::find($id);
            
            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 400);
            }

        if(Auth::user()->id==$idUser){
            

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
                'name' => 'required|string|min:3|max:20|unique:sales,name,' . $sale->id,
             ]); 
            
        
            $updated = $sale->fill($request->all())->save();
        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'La venta no puede ser actualizada'
                ], 500);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina a una venta al modificar el estado de venta de una pieza o
     * al eliminar dicha pieza.
     * Puede acceder cualquier usuario logado y solo tendrá acceso a sus ventas.
     * Además cambia el estado de venta de la pieza.
     */
    public function destroyMySale($idUser,$id)
    {
        if(Auth::user()->id==$idUser){
            $piece = Piece::find($id);
 
            //Sino existe la pieza o existe pero no está vendida
            if (!$piece || !$piece->sold) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 400);
            }

            $sale = Sale::pieceId($id);
            $piece->sold=false;
                
            if ($sale->delete()) {  

                if($piece->save()){
                    return response()->json([
                        'success' => true,
                        'message' => 'La pieza '.$piece->name.' se ha actualizado correctamente a no vendida',
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Se ha creado la venta pero ha habido un error con la actualización del estado de venta de la pieza '.$piece->name.".",
                    ], 400);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'La pieza no ha podido ser actualizada'
                ], 500);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La venta no puede ser eliminada'
                ], 500);
            }
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }


    /**
     * Crea una nueva venta al modificar el estado de venta de una pieza. 
     * Puede acceder cualquier usuario logado y solo tendrá acceso a sus ventas.
     * Además cambia el estado de venta de la pieza.
     */
    public function createMySale($idUser,$idPiece,Request $request)
    {
        if(Auth::user()->id==$idUser){
            $piece = Piece::find($idPiece);
            
            if (!$piece) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pieza no encontrada'
                ], 400);
            }

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',//Valida decimal
                'name' => 'required|string'
             ]); 
            
            $sale = new Sale(); 
            $sale->name = $request->name;
            $sale->price = $request->price;
            $sale->user_id = $piece->user_id;
            $sale->piece_id = $piece->id;
            $sale->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $sale->updated_at = Carbon::now()->format('Y-m-d H:i:s');

            
            $piece->sold=true;

            if ($sale->save()) { 
                 if($piece->save()){
                    return response()->json([
                        'success' => true,
                        'message' => 'La pieza '.$piece->name.' se ha actualizado correctamente a no vendida',
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Se ha creado la venta pero ha habido un error con la actualización del estado de venta de la pieza '.$piece->name.".",
                    ], 400);
                }
               
                return response()->json([
                    'success' => true,
                    'message' => 'La venta '.$sale->name.' ha sido creada correctamente',
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

}
