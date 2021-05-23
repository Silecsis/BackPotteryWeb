<?php

namespace App\Http\Controllers;

/**
 * Controlador de autorización.
 * Controla los usuarios logados.
 */


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessagePasswordRequest;

class PassportAuthController extends Controller
{
    /**
     * Registra un nuevo usuario. 
     * Opción desde login.
     * Puede acceder cualquier usuario.
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|min:6',
            'email' => 'required|string|email|max:255|min:6|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation'=>'required_with:password|same:password|min:8',
            'nick' => 'required|string|max:255|min:2'
        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = "user";
        $user->nick = $request->nick;
        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->remember_token = 'remember'.$user->nick;
        $user->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        // $token = $user->createToken('LaravelAuthApp')->accessToken;
        // return response()->json(['token' => $token], 200);
        
        if ($user->save())
            return response()->json([
                'success' => true,
                'data' => $user->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Se ha producido un error a la hora de realizar el registro'
            ], 500);
    }
 
    /**
     * Login.
     * Permite al usuario a entrar.
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

    /**
     * Recuperará la contraseña mediante el email.
     * 
     * Cambiará la contraseña del usuario por otra nueva y
     * se le envia un correo al usuario con dicha contra.
     * 
     * NOTA: Para cambiar el gmail utilizado para enviar correos,
     * cambiar las siguientes variables de .env :
     *  MAIL_HOST=smtp.mailtrap.io
     *  MAIL_PORT=2525
     *  MAIL_USERNAME=8db0fe1f6ff471
     *  MAIL_PASSWORD=d2ce956b9e3cb1
     *
     * @return void
     */
    public function recuperate(Request $request)
    {
        $emailUser = $request->email;

        
        //Si el user está en la bd, se genera una contraseña nueva aleatoria.
        $passwordNew=uniqid("",true); 
        //Luego se cambia esta contraseña en la bd.
        $changePassword = UserController::changePassword($emailUser,$passwordNew);
        
        if($changePassword['success']){
            Mail::to($emailUser)->send(new MessagePasswordRequest($passwordNew));
            return response()->json([
                'success' => true,
                'message' => 'Correo enviado',
            ]); 
        }else{
            
            return response()->json([
                'success' => false,
                'message' => $changePassword['message'],
            ], 500);
        } 
    }
}
