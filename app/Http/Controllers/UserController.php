<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD de usuarios desde el administrador.
 * Modificación de perfil de cada usuario.
 */
class UserController extends Controller
{
    /**
     * Lista todos los usuarios.
     * Solo puede acceder el tipo administrador.
     *
     */
    public function all()
    {
        if(Auth::user()->type=='admin'){
            $users=User::all();
            return response()->json($users);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        
    }

    /**
     * Devuelve un usuario localizado por el id desde la lista de usuarios.
     * Solo puede acceder el tipo administrador.
     *
     */
    public function show($id)
    {
        if(Auth::user()->type=='admin'){
            $user = User::find($id);
 
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado '
                ], 400);
            }
     
            return response()->json([
                'success' => true,
                'data' => $user->toArray()
            ], 200);

        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        
    }


    /**
     * Devuelve los datos del usario logado.
     * Desde opción del la barra de navegación.
     * Solo puede acceder el usuario logado.
     *
     */
    public function showProfile($id)
    {
        $user = User::find($id);
 
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado '
            ], 400);
        }

        if(Auth::user()->id==$user->id){
           
            return response()->json([
                'success' => true,
                'data' => $user->toArray()
            ], 200);

        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        
    }

    /**
     * Edita el usuario desde la lista de usuarios.
     * Solo puede acceder el tipo administrador.
     *
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->type=='admin'){
            $user = User::find($id);
 
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 400);
            }

            //Si existe, primero validamos.   
            $request->validate([
                'name' => 'required|string|max:255|min:6',
                'email' => 'required|string|email|max:255|min:6|unique:users,email,' . $user->id,
                'type' => 'required|string|max:10|in:admin,user' . $user->id,
                'nick' => 'required|string|max:255|min:4'
                //'img' => 'required|min:4',
             ]); 
        
            $updated = $user->fill($request->all())->save();
        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no puede ser actualizado'
                ], 500);

        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        
    }

    /**
     * Edita el usuario logado desde la opción de la barra de navegación.
     * Solo puede acceder el usuario logado.
     *
     */
    public function updateProfile(Request $request, $id)
    {
        $user = User::find($id);
 
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 400);
        }

        if(Auth::user()->id==$user->id){
            
            //Si lo encuentra, primero validamos.   
            $request->validate([
                'name' => 'required|string|max:255|min:6',
                'email' => 'required|string|email|max:255|min:6|unique:users,email,' . $user->id,
                'password' => 'required|string|confirmed|min:8',
                'password_confirmation'=>'required_with:password|same:password|min:8',
                'nick' => 'required|string|max:255|min:4'
            ]);  
        
            $updated = $user->fill($request->all())->save();
        
            if ($updated)
                return response()->json([
                    'success' => true
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no puede ser actualizado'
                ], 500);

        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Crea un nuevo usuario. 
     * Opción desde la vista de listar usuarios.
     * Solo puede acceder el tipo administrador.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        if(Auth::user()->type=='admin'){
            $request->validate([
                'name' => 'required|string|max:255|min:6',
                'email' => 'required|string|email|max:255|min:6|unique:users,email',
                'password' => 'required|string|min:8',
                'password_confirmation'=>'required_with:password|same:password|min:8',
                'type' => 'required|string|max:10|in:admin,user',
                'nick' => 'required|string|max:255|min:2'
             ]);

            
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->type = $request->type;
            $user->nick = $request->nick;
            $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->remember_token = 'remember'.$user->nick;
            $user->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            
            if ($user->save())
                return response()->json([
                    'success' => true,
                    'data' => $user->toArray()
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Se ha producido un error a la hora de crear el usuario'
                ], 500);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina a un usuario desde la lista de usuarios.
     * Solo puede acceder el tipo administrador.
     */
    public function destroy($id)
    {
        $user = User::find($id);
 
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 400);
        }
 
        if ($user->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'El usuario con correo '.$user->email.' ha sido eliminado correctamente',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no puede ser eliminado'
            ], 500);
        }
    }
}
