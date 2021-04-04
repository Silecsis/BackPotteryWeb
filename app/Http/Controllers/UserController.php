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
     * Lista todos los usuarios
     *
     * @return void
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
     * Devuelve un usuario localizado por el id.
     *
     * @param User $id
     * @return void
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
     * Edita el usuario
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

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'type' => 'required',
            'nick' => 'required'
         ]);
 
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->type = $request->type;
        $user->nick = $request->nick;
        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->remember_token = 'remember'.$user->nick;
 
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
    }

    /**
     * Elimina a un usuario de la bbdd.
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
