<?php

use dopForma\models\user;
use dopForma\models\passRestore;
use Phalcon\Db\Column;
use dopForma\tools\responses\factory as respFac;
use dopForma\tools\useful;

$app->post(
    "/user",
    function () use ($app) {
    
    $data=$app->request->getJsonRawBody();
    $user= new user();
    $user->setEmail($data->email);
    $user->setAspId($data->asp_id);
    $user->setPass($data->password_);
    if(!$user->create()){
        throw new Exception(implode(',', $user->getMessages()));
    }
    
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->message='ok';
        $resp->data='ok';
        $resp->send();    
    }
);

$app->get(
    '/user/{id}',
    function ($id)  {
    $u=user::findFirst($id);
    //echo "<pre>".print_r($u,1)."<pre>";
  //  echo " {$u->email} {$u->password_} {$u->id} {$u->asp_id} ".PHP_EOL;
      //echo " $id ".PHP_EOL;
    
    }
);

$app->get(
    '/users',
    function()use($app)  {
    $us=user::find();
    //echo "<pre>".print_r($u,1)."<pre>";
    $res=[];
    foreach($us as $u){
        $res[]=["email"=>$u->email,
                "id"=>$u->id,
                "aspId"=>$u->asp_id
                 ];
       // echo " {$u->email} {$u->password_} {$u->id} {$u->asp_id} ".PHP_EOL;
    }        
        
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->message='ok';
        $resp->data=$res;
        $resp->send();    
    
    }
);


$app->post('/users',
        function () use($app){
          
        $d=json_decode( $app->request->get('data'),1);
        $res=['success'=>true,
              'error'=>'',
              'passwords'=>[] 
             ]; 

        $users= user::find(" id > 0  ");
        $refUsers=[];
        for($i=0, $l=count($users); $i<$l; $i++){
        $refUsers[$users[$i]->asp_id]=$users[$i];    
        }
        
        
        for($i=0, $l=count($d); $i<$l; $i++){
            
            
            if( array_key_exists($d[$i]['aspirant_id'], $refUsers  ) ){
                $exsUser=$refUsers[$d[$i]['aspirant_id']];
                $exsUser->setEmail($d[$i]['e_mail']);
                $exsUser->setFio($d[$i]['fio']);
                if(!$exsUser->save()){
                    $res["success"]=false;
                    $res['error'].=implode(',',$exsUser->getMessages() );        
                }else{
                $res['passwords'][$d[$i]['aspirant_id']]='';
                    
                }
            }else{
            
            $u= new user();
            $u->setAspId($d[$i]['aspirant_id']);
            $u->setEmail($d[$i]['e_mail']);
            $u->setFio($d[$i]['fio']);
            $pass=$u->setPass();
            if(!$u->create()){
               // echo " {$d[$i]['aspirant_id']} -- {$d[$i]['e_mail']} <br>";
               $res["success"]=false;
               $res['error'].=  implode(',', $u->getMessages())." {$d[$i]['aspirant_id']}  {$d[$i]['e_mail']};  ";
            }else{
                $res['passwords'][$d[$i]['aspirant_id']]=$pass;
            }    
            }
            
        }  
       
        
        $resp=respFac::create('ext',$app->request);
        $resp->success=$res["success"];
        $resp->message='ok';
        $resp->data= (!$res["success"])?$res['error']:'';    //$res['passwords'];
        $resp->send();    
          
        }
        );



$app->post(
    '/users/auth',
    function() use ($app)  {
     
     //user::getUserById($app->request->get('userId'));
     
    $sec=$app->user->setSecret();
//    useful::show($app->user);
//     die();
    if(!$app->user->save()){
        throw new \Exception(implode(' ',$app->user->getMessages()));
    }
    $resp=respFac::create('ext', $app->request  );
    $resp->success=true;
    $resp->data= $app->user->id.'_'.$sec;
    $resp->send();   
    }
);


/// Востановление и Установка пароля 
$app->get(
         '/users/setNewPass/{code}'
        ,function($code) use ($app){
           try{
               
           $pr=  passRestore::findFirst(" code='$code' ");
           if(!$pr){ throw new \Exception('код не знайдено, пароль не змінено'); }
           
           $user=  user::findFirst($pr->user_);

           $user->setPass($pr->pass);
       //    $user->setFio($user->fio.'_'.$pr->sec);
           $user->setSecret($pr->sec);
           if(
              !$pr->delete()
                   ){ throw new Exception( (implode('<br>',$pr->getMessages())) ) ; }
           $res=
               (!$user->save())
                   ?
                  implode('<br>',$user->getMessages()) 
                   :
                   "Пароль Змінено"
                   ;
           
           }catch(\Exception $e){
               die($e->getMessage() );
           }
           die($res);         
        } 
        );

$app->post(
    '/users/setNewPass',
    function() use ($app)  {
    $id=$app->request->get('userId');
    $user=  user::findFirst((int)$id);
    if(!$user)        throw  new \Exception ('Користувач не знайден');
        $key=$user->setSecret();
    $pwd=$user->setPass();
    $host=$app->request->getHttpHost();
    
    
    $pr= new passRestore();
    $pr->setCode()
       ->setSec($key)
       ->setPass( $pwd )
       ->setUser($user->id)
       ->create();
    $message = Swift_Message::newInstance('зміна паролю')
     ->setFrom(array('aspirant_office@ukr.net' ))
     ->setTo(array($user->email))
     ->setBody(" Зміна паролю! 
                 Новый пароль : $pwd   ".(($id==9)? ' key: '.$key : ''  ). "
                 Для зміни паролю  перейдіть за посиланням  $host/users/setNewPass/$pr->code    
                ");
                
    
    
    $res= Swift_Mailer::newInstance($app->smtpTramsport)->send($message);
   
    $resp=respFac::create('ext', $app->request  );
    $resp->success=(bool)$res;
    $resp->send();
    
    }
);






