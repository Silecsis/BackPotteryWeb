<?php

namespace App\Http\Controllers;

/**
 * Controlador de autorización.
 * Controla los usuarios logados.
 */

use Illuminate\Http\Request;
use App\Models\User;

class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('LaravelAuthApp')->accessToken;
 
        return response()->json(['token' => $token], 200);
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            $user = User::emailExacto($request->email)->get();

            return response()->json(['token' => $token, 'user'=>$user[0]], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }   

    /**
     * Destruye la sesion
     */
    public function logout( )
    {
        auth('api')->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out', 200);
    }
}
