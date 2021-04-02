<?php

namespace App\Http\Controllers;

use App\Models\User;

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
        $users=User::all();

        return response()->json($users);;
    }

    /**
     * Devuelve un usuario localizado por el id.
     *
     * @param User $id
     * @return void
     */
    public function show($id)
    {
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
        ], 400);
    }

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
