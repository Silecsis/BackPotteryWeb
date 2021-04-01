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
    /**
     * Lista todas las piezas del sistema.
     * Solo podrá acceder los usuarios de tipo admin.
     * Estos solo podrán ver.
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

        //Necesitaremos esta variable para mandar a la vista los usuarios y listarlos.
        $usersAll=User::all();
        
        
        //Si no hay request o la request tiene más elementos a parte de la paginacióm, filtra.
        if(!$request|| isset($arr) && $request != $arr ){
            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $idUser= $request->get('buscaUser');
            $vendido= $request->get('buscaVendido');
            $fecha= $request->get('buscaFechaLogin');
            
            //La pieza con los filtros:
            $pieces = Piece::nombre($nombre)->userId($idUser)->vendido($vendido)->fecha($fecha)->paginate($page);

            //Hacemos control
            if(count($pieces)==0){
                $array=[
                    'success' => false,
                    'message' => 'No existen piezas con dichos datos en nuestra base de datos',
                    'usersAll' => $usersAll,
                ];
            }else{
                //Buscamos los usuarios y las piezas:
                $array=[
                    'success' => true,
                    'pieces'=>$pieces,
                    'usersAll' => $usersAll
                ];
            }
            //Y mandamos a la vista
            return view('/pieces/pieces',compact('pieces'),$array);
        }else{
        //Si no tiene request o la que tiene es solo de paginacion:
            //Listamos todas las piezas
            $pieces = Piece::paginate($page);

            //Hacemos control
            if(!$pieces){
                $array=[
                    'success' => false,
                    'message' => 'No existen piezas con dichos datos en nuestra base de datos',
                    'usersAll' => $usersAll
                ];
            }else{
                //Buscamos los usuarios y las piezas:
                
                $array=[
                    'success' => true,
                    'pieces'=>$pieces,
                    'usersAll' => $usersAll
                ];
            }
            //Devolvemos vista
            return view('/pieces/pieces',$array);
        }
    }

    /**
     * Lista las piezas del usuario registrado
     *
     * @return \Illuminate\Http\Response
     */
    public function listarMisPiezas(Request $request)
    {
        if($request->get('pagination')){
            $page=$request->get('pagination');
            $arr['pagination']=$request->get('pagination');
        }else{
            $page=Config::get('constants.pagination');
            $arr['pagination']= $page; 
        }

        //Si no hay request o la request tiene más elementos a parte de la paginación, filtra.
        if(!$request|| isset($arr) && $request != $arr ){
            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $idUser= $request->get('buscaUser');
            $vendido= $request->get('buscaVendido');
            $fecha= $request->get('buscaFechaLogin');
            
            //La pieza con los filtros. Siempre lista los del usuario logueado
            $pieces = Piece::userId(Auth::user()->id)->nombre($nombre)->vendido($vendido)->fecha($fecha)->paginate($page);

            //Hacemos control
            if(count($pieces)==0){
                $array=[
                    'success' => false,
                    'message' => 'No existen Piezas.',
                ];
            }else{

                $array=[
                    'success' => true,
                    'pieces'=>$pieces,
                ];
            }
            //Y mandamos a la vista
            return view('/pieces/my-pieces',compact('pieces'),$array);
        }else{
        //Si no tiene request o la que tiene es solo de paginacion:
            //Listamos todas las piezas del usuario
            $pieces = Piece::userId(Auth::user()->id)->paginate($page);

            //Hacemos control
            if(!$pieces){
                $array=[
                    'success' => false,
                    'message' => 'No existen piezas'
                ];
            }else{

                $array=[
                    'success' => true,
                    'pieces'=>$pieces,
                ];
            }
            //Devolvemos vista
            return view('/pieces/my-pieces',$array);
        }
    }


    /**
     * Manda a la vista de 'Vendida'.
     * Solo podrá hacerlo si el id del usuario logueado
     * concuerda con el user_id de la pieza.
     *
     * @return \Illuminate\Http\Response
     */
    public function sold(Request $request)
    {
        $piece = Piece::find($request->get('id'));

        //Sino existe, mandamos a página de error.
        if (!$piece) {
            $array=[
                'window'=>'Mis piezas -> ¡Vendida!',
                'message' => 'Pieza no encontrada'
            ];

            return view('/extras/error',$array);

        }else if($piece && $piece->user_id != Auth::user()->id){
            //Si existe pero no la pieza no es del usuario, error.
            $array=[
                'window'=>'Mis piezas -> ¡Vendida!',
                'message' => 'No puede acceder a una pieza que no es suya.'
            ];

            return view('/extras/error',$array);
        }else{
        //Si todo va bien, mandamos a la pagina de venta.
            $array=[
                "piece"=>$piece
            ];
            return view('/pieces/sold',$array);
        }
    }

    /**
     * 
     * Muestra la ventada de edicion de la pieza y le pasa los datos de esta.
     * Solo podrá hacerlo si el id del usuario logueado
     * concuerda con el user_id de la pieza.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $piece = Piece::find($request->get('id'));
        //Control de que no pueda acceder un usuario que no tenga dicha pieza y de que la pieza exista
        if(Auth::user()->id == $piece->user_id && $piece){
    
              //Si existe, mandamos a la página de edición de pieza con los datos de la pieza.
              $array=[
                "piece"=>$piece
            ];
            return view('/pieces/edit-piece',$array);
            
        }else if(!$piece){
            $array=[
                'window'=>'Mis piezas -> Editar pieza',
                'message' => 'No existe la pieza que desea editar.'
            ];

            return view('/extras/error',$array);
        }else{
            $array=[
                'window'=>'Mis piezas -> Editar pieza',
                'message' => 'No puede editar una pieza que no es suya.'
            ];

            return view('/extras/error',$array);
        }
    }

    /**
     * Edita la pieza.
     * Solo podrá hacerlo si el id del usuario logueado
     * concuerda con el user_id de la pieza.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Piece $piece)
    {
        $piece = Piece::find($request->get('id'));
        //Control de que no pueda acceder un usuario que no tenga dicha pieza y de que la pieza exista
        if(Auth::user()->id == $piece->user_id && $piece){
    
              //Si existe la pieza, primero validamos.   
              $request->validate([
                'name' => 'required|string|max:255|min:6|unique:users,email,' . $piece->id,
                'description' => 'required|string|'
             ]); 

            //Subir la imagen
            $image= $request->file('image'); 
            // Si recibimos un objeto imagen tendremos que utilizar el disco para almacenarla
            // Para ello utilizaremos un objeto storage de Laravel
            if($image){
                // Generamos un nombre único para la imagen basado en time() y el nombre original de la imagen
                $image_name =  time() . $image->getClientOriginalName();
                $image_delete= $piece->img;
                
                // Seleccionamos el disco virtual users, extraemos el fichero de la carpeta temporal
                // donde se almacenó y guardamos la imagen recibida con el nombre generado
                Storage::disk('pieces')->put($image_name, File::get($image));

                //Eliminamos la que tenia antes
                
                Storage::disk('pieces')->delete($image_delete);

                $piece->img = $image_name;   
            } 
            
            $piece->name = $request->name;
            $piece->description = $request->description;

            //Si probamos a guardar y funciona, lo redirigimos a la vista de edicion con un mensaje.
            if ($piece->save()) { 
                $array=[
                    'id'=>$piece->id
                ];
                return redirect()->route('edit.piece', $array)->with(['status' => 'La pieza de nombre '.$piece->name.' ha sido actualizada correctamente']);

            } else {
            //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
                $array=[
                    'window'=>'Mis piezas -> Edición de pieza',
                    'message' => 'La pieza no puede ser editada'
                ];

                return view('/extras/error',$array);
            }
        
        //Si la pieza no existe    
        }else if(!$piece){
            $array=[
                'window'=>'Mis piezas -> Edición de pieza',
                'message' => 'No existe la pieza que desea editar.'
            ];

            return view('/extras/error',$array);
        }else{
        //Si la pieza existe y la pieza no es del usuario
            $array=[
                'window'=>'Mis piezas -> Edición de pieza',
                'message' => 'No puede editar una pieza que no es suya.'
            ];

            return view('/extras/error',$array);
        }
    }
    

    /**
     * CManda a la vista.
     *
     * @return \Illuminate\Http\Response
     */
    public function newPiece()
    {
        $materials=Material::all();
        $array=[
            'materials'=>$materials
        ];

        return view('/pieces/new-piece',$array);
      
    }

    /**
     * Crea una nueva pieza.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $piece = new Piece();
    
         //Primero validamos.   
         $request->validate([
           'name' => 'required|string|max:255|min:6|unique:users,email,',
           'description' => 'required|string|',
           'image'=>'required',
           'materials[]'=>'min:1',
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

          //Por defecto, la pieza no estará vendida y tendrá el id del usuario logueado.
          $piece->user_id = Auth::user()->id;
          $piece->sold = 0;

          
          $materials=$request->materials;

          //Si probamos a guardar y funciona, lo redirigimos a la vista de nueva pieza con un mensaje.
          if ($piece->save() && $materials!=null) { 
                //Le asignamos los materiales a la pieza.
                foreach($materials as $idMaterial){
                      $piece->materials()->attach($idMaterial);
                }


              return redirect()->route('new.piece')->with(['status' => 'La pieza se ha creado correctamente']);

          } else if($materials==null){
            return redirect()->route('new.piece')->with(['statusError' => 'Debes marcar un material al menos.']);
          }else {
          //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
              $array=[
                  'window'=>'Mis piezas -> Nueva pieza -> Creación',
                  'message' => 'La pieza no se ha podido crear'
              ];

              return view('/extras/error',$array);
          }
      
    }

    /**
     * Elimina la pieza.
     * Solo podrá hacerlo si el id del usuario logueado
     * concuerda con el user_id de la pieza.
     *
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $piece = Piece::find($request->get('id'));
        //Control de que no pueda acceder un usuario que no tenga dicha pieza y de que la pieza exista
        if(Auth::user()->id == $piece->user_id && $piece){
    
              //Si existe, recogemos el ombre de la pieza.    
              $name=$piece->name;
              $image_delete= $piece->img; //guardamos la imagen a eliminar

              //Si probamos a eliminar y funciona, lo redirigimos al listado de las piezas.
              if ($piece->delete()) { 
                  //Eliminamos la imagen que tenia
                    Storage::disk('pieces')->delete($image_delete);
                
                  return redirect()->route('my.pieces')->with(['status' => 'La pieza '.$name.' ha sido eliminada correctamente']); 
              } else {
              //Sino funciona el delete, se le manda a la vista de error con el mensaje.
                  $array=[
                      'window'=>'Mis piezas -> Eliminación de pieza',
                      'message' => 'La pieza '.$piece->name.' no puede ser eliminada'
                  ];

                  return view('/extras/error',$array);
              }
            
        }else if(!$piece){
        //Si la pieza no existe
            $array=[
                'window'=>'Mis piezas -> Eliminación pieza',
                'message' => 'No existe la pieza que desea eiminar.'
            ];

            return view('/extras/error',$array);
        }else{
        //Si existe y es un usuario que no tiene esa pieza.
            $array=[
                'window'=>'Mis piezas -> Eliminación pieza',
                'message' => 'No puede eliminar una pieza que no es suya.'
            ];

            return view('/extras/error',$array);
        }
    }

     /**
     * Muestra la pieza con sumo de detalles.
     * También lista los materiales empleados en la pieza.
     *
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        $id=$request->get('id');
        $piece = Piece::find($id);

        //Sino existe, mandamos a página de error.
        if (!$piece) {
            $array=[
                'window'=>'Usuario -> Piezas de cada usuario -> Ver en detalle',
                'message' => 'Pieza no encontrada'
            ];

            return view('/extras/error',$array);
        }else{
            //Si existe, mandamos a la página de edición de usuario con los datos del usuario.
            
            $user=User::find($piece->user_id);
            $array=[
                "piece"=>$piece,
                "materials"=>$piece->materials,
                "user"=>$user
            ];
            return view('/pieces/detail',$array);
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
