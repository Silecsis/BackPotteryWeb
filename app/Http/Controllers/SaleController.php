<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;


/**
 * Lectura, creación y modificación de venta. 
 * Las ventas se borran automáticamente cuando se borra la pieza.
 * Esto podrán hacerlo solo los usuarion que tengan dichas ventas registradas
 */
class SaleController extends Controller
{
    public function all()
    {
        $users=User::all();
        $pieces=Piece::all();
        $sales=Sale::all();

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


        return response()->json($sales);
    }
    
    /**
     * Devuelve un usuario localizado por el id.
     *
     */
    public function show($id)
    {
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
    }

    /**
     * Edita el usuario
     *
     */
    public function update(Request $request, $id)
    {
        $sale = Sale::find($id);
 
        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 400);
        }
 
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
    }

}
