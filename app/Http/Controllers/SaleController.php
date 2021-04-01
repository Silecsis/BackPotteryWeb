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
    /**
     * Solo usuarios de tipo admin.
     * Muestra todas las ventas.
     * Solo las lista, no puede editar, crear ni borrar.
     *
     * @return \Illuminate\Http\Response
     */
    public function listar(Request $request)
    {
        if($request->get('pagination')){
            $page=$request->get('pagination');
            $arr['pagination']=$request->get('pagination');
        }else{
            $page=Config::get('constants.pagination');
            $arr['pagination']= $page; 
        }

        //Necesitaremos estas variables para mandar a la vista los usuarios y piezas y listarlos.
        $usersAll=User::all();
        $piecesAll = Piece::all();
        
        
        //Si no hay request o la request tiene más elementos a parte de la paginacióm, filtra.
        if(!$request|| isset($arr) && $request != $arr ){
            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $idUser= $request->get('buscaUser');
            $idPiece= $request->get('buscaPiece');
            $fecha= $request->get('buscaFechaLogin');
            $precio= $request->get('buscaPrecio');
            
            //La venta con los filtros:
            $sales = Sale::nombre($nombre)->userId($idUser)->pieceId($idPiece)->fecha($fecha)->precio($precio)->paginate($page);

            //Hacemos control
            if(count($sales)==0){
                $array=[
                    'success' => false,
                    'message' => 'No existe venta con dichos datos en nuestra base de datos',
                    'usersAll' => $usersAll,
                    'piecesAll' => $piecesAll 
                ];
            }else{

                $array=[
                    'success' => true,
                    'sales'=>$sales,
                    'usersAll' => $usersAll,
                    'piecesAll' => $piecesAll
                ];
            }
            //Y mandamos a la vista
            return view('/sales/sales',compact('sales'),$array);
        }else{
        //Si no tiene request o la que tiene es solo de paginacion:
            //Listamos todas las ventas
            $sales = Sale::paginate($page);

            //Hacemos control
            if(!$sales){
                $array=[
                    'success' => false,
                    'message' => 'No existen ventas',
                    'usersAll' => $usersAll,
                    'piecesAll' => $piecesAll
                ];
            }else{

                $array=[
                    'success' => true,
                    'sales'=>$sales,
                    'usersAll' => $usersAll,
                    'piecesAll' => $piecesAll
                ];
            }
            //Devolvemos vista
            return view('/sales/sales',$array);
        }
       
    }

     /**
     * Muestra las ventas del usurio logueado.
     * Podrá editar la venta.
     *
     * @return \Illuminate\Http\Response
     */
    public function listarMisVentas(Request $request)
    {
        if($request->get('pagination')){
            $page=$request->get('pagination');
            $arr['pagination']=$request->get('pagination');
        }else{
            $page=Config::get('constants.pagination');
            $arr['pagination']= $page; 
        }

        //Necesitaremos esta variable para mandar a la vista las piezas y listarlas.
        $piecesAll = Piece::all();

        //Si no hay request o la request tiene más elementos a parte de la paginacióm, filtra.
        if(!$request|| isset($arr) && $request != $arr ){
            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $idPiece= $request->get('buscaPiece');
            $fecha= $request->get('buscaFechaLogin');
            $precio= $request->get('buscaPrecio');
            
            //La venta con los filtros. Siempre se buscará segun el id del usuario identificado
            $sales = Sale::userId(Auth::user()->id)->nombre($nombre)->pieceId($idPiece)->fecha($fecha)->precio($precio)->paginate($page);

            //Hacemos control
            if(count($sales)==0){
                $array=[
                    'success' => false,
                    'message' => 'No existen ventas.',
                    'piecesAll' => $piecesAll 
                ];
            }else{

                $array=[
                    'success' => true,
                    'sales'=>$sales,
                    'piecesAll' => $piecesAll
                ];
            }
            //Y mandamos a la vista
            return view('/sales/my-sales',compact('sales'),$array);
        }else{
        //Si no tiene request o la que tiene es solo de paginacion:
            //Listamos todas las ventas del usuario identificado
            $sales = Sale::userId(Auth::user()->id)->paginate($page);

            //Hacemos control
            if(!$sales){
                $array=[
                    'success' => false,
                    'message' => 'No existen ventas',
                    'piecesAll' => $piecesAll
                ];
            }else{

                $array=[
                    'success' => true,
                    'sales'=>$sales,
                    'piecesAll' => $piecesAll
                ];
            }
            //Devolvemos vista
            return view('/sales/my.sales',$array);
        }
       
    }

    /**
     * Edita la venta.
     * Solo lo puede hacer el usuario que tenga dicha venta 
     * Solo el nombre y el precio.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $sale = Sale::find($request->get('id'));

        //Control de que lo edita el usuario de la venta y de que la venta existe
        if(Auth::user()->id == $sale->user_id && $sale){

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
                'name' => 'required|string'
             ]); 
            
            $sale->name = $request->name;
            $sale->price = $request->price;

            //Si probamos a crear y funciona, lo redirigimos a la vista de edicion de venta.
            if ($sale->save()) {

                //Redirigimos a la vista de mis piezas con un mensaje
                return redirect()->route('edit.sale',['id'=>$sale->id])->with(['status' => 'La venta '.$sale->name.' se ha editado correctamente']);

            } else {
            //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
                $array=[
                    'window'=>'Mis ventas -> Edición de venta',
                    'message' => 'No se ha podido editar la venta'
                ];

                return view('/extras/error',$array);
            }
            
        }else if(!$sale){
            $array=[
                'window'=>'Mis ventas -> Edición de venta',
                'message' => 'No se ha encontrado la venta a editar.'
            ];

            return view('/extras/error',$array);
        }else{
            $array=[
                'window'=>'Mis ventas -> Edición de venta',
                'message' => 'No puede editar una venta que no es suya.'
            ];

            return view('/extras/error',$array);
        }
        
    }

    /**
     * Muestra la vista de edición de venta.
     * Solo lo puede hacer el usuario que tenga dicha venta 
     * Solo edita el nombre y el precio.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $sale = Sale::find($request->get('id'));

        //Control de que lo edita el usuario de la venta y de que la venta existe
        if(Auth::user()->id == $sale->user_id && $sale){

             $array=[
                "sale"=>$sale
            ];
            return view('/sales/edit-sale',$array);
            
        }else if(!$sale){
            $array=[
                'window'=>'Mis ventas -> Editar venta',
                'message' => 'No se ha encontrado la venta a editar.'
            ];

            return view('/extras/error',$array);
        }else{
            $array=[
                'window'=>'Mis ventas -> Editar venta',
                'message' => 'No puede editar una venta que no es suya.'
            ];

            return view('/extras/error',$array);
        }
    }

    /**
     * 
     * Crea una nueva venta
     * Solo lo puede hacer el usuario que tenga la pieza que se quiere vender.
     * Se realiza desde la vista de my.pieces 
     * Recoge el id de la pieza para poder crear la venta.
     * 
     * Además modifica el campo sold de la pieza a true.
     * 
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $piece=Piece::find($request->get('piece_id'));

         //Control de que sea el usuario de la pieza, que exista la pieza y que no esté ya vendida
         if(Auth::user()->id == $piece->user_id && $piece 
            && !$piece->sold){
    
            $sale = new Sale();

            $request->validate([
                'price' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',//Valida decimal
                'name' => 'required|string'
             ]); 
            
            $sale->name = $request->name;
            $sale->price = $request->price;
            $sale->user_id = Auth::user()->id;
            $sale->piece_id = $piece->id;

            //Cambiamos el boolean de la venta
            $piece->sold=true;

            //Si probamos a crear y guardar el estado de la pieza y funciona, lo redirigimos a la vista del listado de piezas con un mensaje.
            if ($sale->save() && $piece->save()) {

                //Redirigimos a la vista de mis piezas con un mensaje
                return redirect()->route('my.pieces',['pagination'=>4])->with(['status' => 'La venta de la pieza '.$piece->name.' se ha creado correctamente']);

            } else {
            //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
                $array=[
                    'window'=>'Mis piezas -> ¡Vendida! -> Confirmación de venta',
                    'message' => 'Ha habido un problema al confirmar la venta'
                ];

                return view('/extras/error',$array);
            }
        }else if(!$piece){
            $array=[
                'window'=>'Mis piezas -> ¡Vendida! -> Confirmación de venta',
                'message' => 'No se ha podido encontrar la pieza de venta.'
            ];

            return view('/extras/error',$array);
        //Si la pieza existe y esta vendida    
        }else if($piece && $piece->sold){
            $array=[
                'window'=>'Mis piezas -> ¡Vendida! -> Confirmación de venta',
                'message' => 'La pieza '.$piece->name.' ya ha sido vendida. Por favor, modifique la venta si es lo que desea.'
            ];

            return view('/extras/error',$array);
        }else{
            $array=[
                'window'=>'Mis piezas -> ¡Vendida! -> Confirmación de venta',
                'message' => 'No puede confirmar una venta que no es suya.'
            ];

            return view('/extras/error',$array);
        }
    }
}
