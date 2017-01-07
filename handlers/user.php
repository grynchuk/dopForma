<?php

use dopForma\models\user;
   
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
        echo json_encode($res);    
    
    }
);







