<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Controlador del modelo Material.
 * Contiene el CRUD de Material.
 * Socios solo pueden ver.
 */
class MaterialController extends Controller
{
    /**
     * Lista todos los materiales existentes
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
        
        
        //Si no hay request o la request tiene más elementos a parte de la paginacióm, filtra.
        if(!$request|| isset($arr) && $request != $arr ){
            //Tipos de filtrado:
            $nombre= $request->get('buscaNombre');
            $tipo= $request->get('buscaTipo');
            $temperatura= $request->get('buscaTemperatura');
            $toxico= $request->get('buscaToxico');
            $fecha= $request->get('buscaFechaCreac');

            //El material con los filtros:
            $materials = Material::nombre($nombre)->tipo($tipo)->temperatura($temperatura)->fecha($fecha)->toxico($toxico)->paginate($page);
                
            //Hacemos control
            if(count($materials)==0){
                $array=[
                    'success' => false,
                    'message' => 'No existe material con dichos datos en nuestra base de datos'
                ];
            }else{
                $array=[
                    'success' => true,
                    'materials'=>$materials
                ];
            }
            //Y mandamos a la vista
            return view('/materials/materials',compact('materials'),$array);
        }else{
        //Si no tiene request o la que tiene es solo de paginacion:
            //Listamos todos los materiales
            $materials = Material::paginate($page);
            //Hacemos control
            if(!$materials){
                $array=[
                    'success' => false,
                    'message' => 'No existe material con dichos datos en nuestra base de datos'
                ];
            }else{
                $array=[
                    'success' => true,
                    'materials'=>$materials
                ];
            }
            //Devolvemos vista
            return view('/materials/materials',$array);
        }
        
    }

    /**
     * Solo accede los usuarios del tipo admin.
     * Crea un nuevo material
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //Control de que no pueda acceder ningun socio
        if(Auth::user()->type == 'admin'){
    
            $material = new Material();

            $request->validate([
                'name' => 'required|string|max:255',
                'type_material' => 'required|string|max:255',
                'temperature' => 'required|int',
                'toxic' => 'required|boolean'
             ]); 
            
            $material->name = $request->name;
            $material->type_material = $request->type_material;
            $material->temperature = $request->temperature;
            $material->toxic = $request->toxic;

            //Si probamos a crear y funciona, lo redirigimos a la vista nuevo material con un mensaje.
            if ($material->save()) {
                return redirect()->route('new.material')->with(['status' => 'El material se ha guardado con éxito.']);

            } else {
            //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
                $array=[
                    'window'=>'Materiales -> Nuevo material -> Creación',
                    'message' => 'El material no se ha podido crear'
                ];

                return view('/extras/error',$array);
            }
        }else{
            $array=[
                'window'=>'Home',
                'message' => 'No tiene permisos de administrador para acceder a dicha URL.'
            ];

            return view('/extras/error',$array);
        }
    }

    /**
     * Solo accede los usuarios del tipo admin.
     * Muestra la ventana de edición del material rellenando
     * los campos con sus datos.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //Control de que no pueda acceder ningun socio
        if(Auth::user()->type == 'admin'){
            //Recogemos el material mediante el id mandado.
            $id=$request->get('id');
            $material = Material::find($id);

            //Sino existe, mandamos a página de error.
            if (!$material) {
                $array=[
                    'window'=>'Materiales -> Edición de material',
                    'message' => 'Material no encontrado'
                ];

            return view('/extras/error',$array);

            }else{
                //Si existe, mandamos a la página de edición de material con los datos del material.
                $array=[
                    "material"=>$material
                ];
                return view('/materials/edit-material',$array);
            }
        }else{
            $array=[
                'window'=>'Home',
                'message' => 'No tiene permisos de administrador para acceder a dicha URL.'
            ];

            return view('/extras/error',$array);
        }
    }

    /**
     * Solo accede los usuarios del tipo admin.
     * Actualiza la edición del material.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material)
    {
        //Control de que no pueda acceder ningun socio
        if(Auth::user()->type == 'admin'){
            //Recogemos el material mediante el id mandado.
            $id=$request->get('id');
            $material = Material::find($id);;
    
            //Sino existe, mandamos a página de error.
            if (!$material) {
                $array=[
                    'window'=>'Materiales -> Edición de material',
                    'message' => 'Material no encontrado'
                ];

            return view('/extras/error',$array);

            //Si todo funciona, editamos el material.
            }else{
                $request->validate([
                    'name' => 'required|string|max:255',
                    'type_material' => 'required|string|max:255',
                    'temperature' => 'required|int',
                    'toxic' => 'required|boolean'
                 ]); 
                
                $material->name = $request->name;
                $material->type_material = $request->type_material;
                $material->temperature = $request->temperature;
                $material->toxic = $request->toxic;

                //Si probamos a actualizar y funciona, lo redirigimos a la vista de edicion de material con un mensaje.
                if ($material->save()) { 
                    $array=[
                        'id'=>$material->id
                    ];
                    return redirect()->route('edit.material', $array)->with(['status' => 'El material ha sido actualizado correctamente']);

                } else {
                //Sino funciona el guardar, se le manda a la vista de error con el mensaje.
                    $array=[
                        'window'=>'Materiales -> Edición de material',
                        'message' => 'El material no puede ser editado'
                    ];

                    return view('/extras/error',$array);
                }
            }
        }else{
            $array=[
                'window'=>'Home',
                'message' => 'No tiene permisos de administrador para acceder a dicha URL.'
            ];

            return view('/extras/error',$array);
        }
    }

    /**
     * Solo accede los usuarios del tipo admin.
     * Elimina un material de la base de datos y luego devuelve al usuario a la 
     * lista de materiales.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //Control de que no pueda acceder ningun socio
        if(Auth::user()->type == 'admin'){
            //Recogemos el material mediante el id mandado.
            $id=$request->get('id');
            $material = Material::find($id);
    
            //Sino existe, mandamos a página de error.
            if (!$material) {
                $array=[
                    'window'=>'Materiales -> Eliminación de material',
                    'message' => 'Material no encontrado'
                ];

            return view('/extras/error',$array);

            }else{
                //Si existe, recogemos el nombre del material.    
                $name=$material->name;

                //Si probamos a eliminar y funciona, lo redirigimos a la vista de listar materiales con un mensaje.
                if ($material->delete()) { 
                    return redirect()->route('materials')->with(['status' => 'El material '.$name.' ha sido eliminado correctamente']);

                } else {
                //Sino funciona el delete, se le manda a la vista de error con el mensaje.
                    $array=[
                        'window'=>'Materiales -> Eliminación de material',
                        'message' => 'El material no puede ser eliminado'
                    ];

                    return view('/extras/error',$array);
                }
            }
        }else{
            $array=[
                'window'=>'Home',
                'message' => 'No tiene permisos de administrador para acceder a dicha URL.'
            ];

            return view('/extras/error',$array);
        }
    }
}
