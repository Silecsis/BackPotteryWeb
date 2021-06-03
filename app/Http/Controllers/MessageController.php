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

             //Los mensajes con los filtros y ordenados de forma descendiente de fecha:
            //Le pasamos los mensajes que no estén borrados de forma lógica
            $msgs = Message::userIdReceived($id)->deleteReceiver('no')->title($title)->read($read)->userIdSender($userSender)->fecha($fecha)->orderBy('created_at','DESC')->get();
            $users=User::all();

            foreach($msgs as $m){
                foreach($users as $u){
                    if($m->user_id_sender == $u->id){
                        $m->emailUser=$u->email;
                    };
                }
            }

            //Le pasamos todo slos mensajes que el usuario ha recibido 
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
                'users'=>$usersSender,
                'usersNewMsg' => $users,
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

             //Los mensajes con los filtros y ordenados de forma descendiente de fecha:
            //Le pasamos los mensajes que no estén borrados de forma lógica
            $msgs = Message::userIdSender($id)->deleteSender('no')->title($title)->read($read)->userIdReceived($userReceiver)->fecha($fecha)->orderBy('created_at','DESC')->get();
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
                'users'=>$usersReceiver,
                'usersNewMsg' => $users,
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

    /**
     * Crea un nuevo mensaje.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function create($idUser,Request $request)
    {   
        if(Auth::user()->id==$idUser){
            $msg = new Message;
            
             //Primero validamos.   
             $request->validate([
               'user_id_receiver' => 'required',
               'title' => 'required|string|min:2|max:30',
               'msg'=>'required|string|min:5|max:255',
            ]); 

            $userId= user::find($request->user_id_receiver);

              if($userId){ 
                $msg->title = $request->title;
                $msg->msg = $request->msg;
                $msg->user_id_receiver = $request->user_id_receiver;
                $msg->user_id_sender = Auth::user()->id;
                $msg->read = 0;
                $msg->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $msg->updated_at = Carbon::now()->format('Y-m-d H:i:s');
  
                if ($msg->save()) { 
                    return response()->json([
                        'success' => true,
                        'message' => 'Mensaje enviado correctamente'
                    ]);
  
                } else {
                  
                  return response()->json([
                      'success' => false,
                      'message' => 'Se ha producido un error a la hora de enviar el mensaje'
                  ], 500);
                } 
              } else{
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario al que se desea enviar el mensaje no se encuentra en nuestra BBDD'
                ], 500);  
              }
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
      
    }

    /**
     * Edita el estado de leido a no leido de un  mensaje.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function editRead($idUser,$id)
    {   
        if(Auth::user()->id==$idUser){
            $msg = Message::find($id); 

            if(!$msg){ 
              return response()->json([
                  'success' => false,
                  'message' => 'El usuario al que se desea enviar el mensaje no se encuentra en nuestra BBDD'
              ], 500);
            } 

            $msg->read = 1;
            $msg->updated_at = Carbon::now()->format('Y-m-d H:i:s');

            if ($msg->save()) { 
                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje editado correctamente'
                ]);

            } else {
              
              return response()->json([
                  'success' => false,
                  'message' => 'Se ha producido un error a la hora de editar el mensaje'
              ], 500);
            } 
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
      
    }

    /**
     * Muestra el total de mensajes sin leer
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     */
    public function msgWithoutRead($idUser)
    {   
        if(Auth::user()->id==$idUser){
            $userAuth = User::find($idUser);
            $msgs = $userAuth->messagesReceived;
            $withoutRead=0;

            if(!$msgs){ 
              return response()->json([
                  'message' => 'No tiene mensajes'
              ]);
            } 

            foreach($msgs as $m){
                //Que no estén leidos ni con el borrado lógico
                if(!$m->read && !$m->delete_receiver){
                    $withoutRead++;
                }
            }

            if ($withoutRead != 0) { 
                return response()->json([
                    'count' => $withoutRead,
                ]);

            } else {
              
                return response()->json([
                    'count' => '0',
                ]);
            } 
        }else{
            return response()->json(['error' => 'Unauthorised'], 401);
        }
      
    }

    /**
     * Realiza un borrado lógico de los mensajes seleccionados con el checkbox.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     * Se realizará el borrado lógico en los mensajes recibidos
     */
    public function deleteLogicReceveided($idUserReceived, Request $request)
    {
        if(Auth::user()->id==$idUserReceived){
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
                }else if ($mAct){
                    $mAct->delete_receiver = true;
                    if($mAct->save()){
                        $successDelete ++;
                    }
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

    /**
     * Realiza un borrado lógico de los mensajes seleccionados con el checkbox.
     * Solo podrá acceder cada usuario logado a sus propios mensajes.
     * Se realizará el borrado lógico en los mensajes enviados
     */
    public function deleteLogicSender($idUserSender, Request $request)
    {
        if(Auth::user()->id==$idUserSender){
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
                }else if ($mAct){
                    $mAct->delete_sender = true;
                    if($mAct->save()){
                        $successDelete ++;
                    }
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
