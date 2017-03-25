<?php

use dopForma\models\user;
use dopForma\models\passRestore;
use Phalcon\Db\Column;
use dopForma\tools\responses\factory as respFac;

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
    
    }
);

$app->get(
    '/user/{id}',
    function ($id)  {
    $u=user::findFirst($id);
    //echo "<pre>".print_r($u,1)."<pre>";
    echo " {$u->email} {$u->password_} {$u->id} {$u->asp_id} ".PHP_EOL;
      //echo " $id ".PHP_EOL;
    
    }
);

$app->get(
    '/users',
    function()  {
    $us=user::find();
    //echo "<pre>".print_r($u,1)."<pre>";
    $res=[];
    foreach($us as $u){
        $res[]=["email"=>$u->email,
                "id"=>$u->id                   
                 ];
       // echo " {$u->email} {$u->password_} {$u->id} {$u->asp_id} ".PHP_EOL;
    }        
        
    //throw new \Exception('ERROR');
    
    echo json_encode($res);    
    
    }
);


$app->post('/users',
        function () use($app){
          
        $d=json_decode( $app->request->get('data'),1);
        $res=['success'=>true,
              'data'=>''
             ]; 
//        var_dump(count($d));
//        die();
//         echo "<pre>".print_r($d,1)."</pre>";
//         die();
        for($i=0, $l=count($d); $i<$l; $i++){
            $u= new user();
            $u->setAspId($d[$i]['aspirant_id']);
            $u->setEmail($d[$i]['e_mail']);
            $u->setFio($d[$i]['fio']);
            $u->setRandPass();
            if(!$u->create()){
               // echo " {$d[$i]['aspirant_id']} -- {$d[$i]['e_mail']} <br>";
               $res["success"]=false;
               $res['data'].=  implode(',', $u->getMessages())." {$d[$i]['aspirant_id']}  {$d[$i]['e_mail']};  ";
            }
        }  
          
          echo json_encode($res);
        }
        );



$app->post(
    '/users/auth',
    function() use ($app)  {
     
    $resp=respFac::create('ext', $app->request  );
    $resp->success=true;
    $resp->send();
    
    //echo json_encode($res);    
    
    }
);



$app->get(
         '/users/setNewPass/{code}'
        ,function($code) use ($app){
    
           $pr=  passRestore::findFirst(" code='$code' ");
           
           
          
           $user=  user::findFirst($pr->user_);

           $user->setPass($pr->pass);
           if(!$pr->delete()){ die(implode('<br>',$pr->getMessages())); }
           $res=
               (!$user->save())
                   ?
                  implode('<br>',$user->getMessages()) 
                   :
                   "Пароль Змінено"
                   ;
             
           die($res);         
        } 
        );

$app->post(
    '/users/setNewPass',
    function() use ($app)  {
   
    
    $id=$app->request->get('userId');
    $user=  user::findFirst((int)$id);
    $user->setRandPass();
    $host=$app->request->getHttpHost();
    
    
    $pr= new passRestore();
    $pr->setCode();
    $pr->setPass(
            $user->password_
            );
    $pr->setUser($user->id);
    $pr->create();
    $message = Swift_Message::newInstance('зміна паролю')
     ->setFrom(array('aspirant_office@ukr.net' ))
     ->setTo(array($user->email))
     ->setBody(" Зміна паролю! 
                 Новый пароль : $user->password_
                 Для зміни паролю  перейдіть за посиланням  $host/users/setNewPass/$pr->code    
                ");
                
    
    
    $res= Swift_Mailer::newInstance($app->smtpTramsport)->send($message);
   
    $resp=respFac::create('ext', $app->request  );
    $resp->success=(bool)$res;
    $resp->send();
    
    }
);






