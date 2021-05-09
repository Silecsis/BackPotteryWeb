<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


/**
 * Lectura, creación y modificación de venta. 
 * Las ventas se borran automáticamente cuando se borra la pieza.
 * Esto podrán hacerlo solo los usuarion que tengan dichas ventas registradas
 */
class SaleController extends Controller
{
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
     * Devuelve un usuario localizado por el id.
     *
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
     * Edita el usuario
     *
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
     * Elimina a un usuario de la bbdd.
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
            
            return response()->json([
                'success' => true,
                'data' => $sale->toArray()
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

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
     * Elimina a un usuario de la bbdd.
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
