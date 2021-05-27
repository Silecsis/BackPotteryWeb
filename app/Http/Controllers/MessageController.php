<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Piece;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class MessageController extends Controller
{
    /**
     * Lista todos los mensajes que ha recibido el usuario.
     * Filtra los mensajes.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function allMsgReceived($id,Request $request)
    {
        //Recogemos el user pasado por id.
        $user=User::find($id);

        if(Auth::user()->id==$user->id){

            //Tipos de filtrado:
            $title= $request->get('buscaTitle');
            $read= $request->get('buscaRead');
            $userSender= $request->get('buscaUser');
            $fecha= $request->get('buscaFechaLogin');

             //Los mensajes con los filtros:
            $msgs = Message::userIdReceived($id)->title($title)->read($read)->userIdSender($userSender)->fecha($fecha)->get();
            $users=User::all();

            foreach($msgs as $m){
                foreach($users as $u){
                    if($m->user_id_sender == $u->id){
                        $m->emailUser=$u->email;
                    };
                }
            }

            //Le pasamos todo slos mensajes que el usuario ha recibido.
            $msgsAll=$user->messagesReceived;
            $usersSender=[];

            foreach($msgsAll as $m){
                foreach($users as $u){
                    if($m->user_id_sender == $u->id){
                        $usersSender[]=[
                            'id'=>$u->id,
                            'email'=>$u->email
                        ];
                    };
                }
            }

            $data=[
                'msgs'=>$msgs,
                'users'=>$usersSender
            ];

            return response()->json($data);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Lista todos los mensajes que ha enviado el usuario.
     * Filtra los mensajes.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function allMsgSended($id,Request $request)
    {
        //Recogemos el user pasado por id.
        $user=User::find($id);

        if(Auth::user()->id==$user->id){

            //Tipos de filtrado:
            $title= $request->get('buscaTitle');
            $read= $request->get('buscaRead');
            $userReceiver= $request->get('buscaUser');
            $fecha= $request->get('buscaFechaLogin');

             //Los mensajes con los filtros:
            $msgs = Message::userIdSender($id)->title($title)->read($read)->userIdReceived($userReceiver)->fecha($fecha)->get();
            $users=User::all();

            foreach($msgs as $m){
                foreach($users as $u){
                    if($m->user_id_receiver == $u->id){
                        $m->emailUser=$u->email;
                    };
                }
            }

            //Le pasamos todo slos mensajes que el usuario ha enviado.
            $msgsAll=$user->messagesSender;
            $usersReceiver=[];

            foreach($msgsAll as $m){
                foreach($users as $u){
                    if($m->user_id_receiver == $u->id){
                        $usersReceiver[]=[
                            'id'=>$u->id,
                            'email'=>$u->email
                        ];
                    };
                }
            }

            $data=[
                'msgs'=>$msgs,
                'users'=>$usersReceiver
            ];

            return response()->json($data);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Devuelve un mensaje localizado por el id desde la lista de "Mensajes".
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function show($idUser,$id)
    {
        if(Auth::user()->id==$idUser){
            $msg = Message::find($id);
            
            if (!$msg) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mensaje no encontrado'
                ], 400);
            }
        
            return response()->json([
                'success' => true,
                'data' => $msg->toArray()
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Elimina los mensajes seleccionados con el checkbox.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function destroy($idUser, Request $request)
    {
        if(Auth::user()->id==$idUser){
            $msgArr = $request -> msgArr;
            $errorNotFound = 0;
            $errorNotDelete = 0;
            $successDelete=0;
 
            if (!count($msgArr) < 0) {
                return response()->json([
                    'success' => false,
                    'messageNotMsg' => 'No hay mensages seleccionados para borrar'
                ], 400);
            }

            foreach ($msgArr as $m){
                $mAct = Message::find($m);

                if(!$mAct){
                    $errorNotFound ++;
                }else if ($mAct && $mAct->delete()){
                    $successDelete ++;
                }else{
                    $errorNotDelete ++;
                }

            }
            
            if ($errorNotFound != 0 || $errorNotDelete != 0) {
                $strErrorNF= (string)$errorNotFound;
                $strErrorND= (string)$errorNotDelete;
                return response()->json([
                    'success' => false,
                    'errorNotFound' =>'No se han encontrado un total de ' . $strErrorNF . ' mensajes en la BBDD',
                    'errorNotDelete' =>'No se han podido eliminar un total de ' . $strErrorND . ' mensajes en la BBDD',
                ], 500);
            } else {
                $strSuccessD= (string)$successDelete;
                return response()->json([
                    'success' => true,
                    'message' => 'Se han eliminado los '.$strSuccessD.' mensajes correctamente',
                ]);
            }
        
            
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
