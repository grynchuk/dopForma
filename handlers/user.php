<?php

use dopForma\models\user;
use Phalcon\Db\Column;

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


$app->post('/users',
        function () use($app){
          
        $d=$app->request->get('data');
        $res=['success'=>true,
              'data'=>''
             ]; 
//        var_dump(count($d));
//        die();
        
        for($i=0, $l=count($d); $i<$l; $i++){
            $u= new user();
            $u->setAspId($d[$i]['aspirant_id']);
            $u->setEmail($d[$i]['e_mail']);
            $u->setRandPass();
            if(!$u->create()){
               $res["success"]=false;
               $res['data']=  implode(',', $u->getMessages());
            }
        }  
          
          echo json_encode($res);
        }
        );



$app->post(
    '/users/auth',
    function() use ($app)  {
   
 $param = [
    "id" => $app->request->get('id'),
    "password" => $app->request->get('password_'),
];

// Привязка типов параметров
$types = [  
    "id" => Column::BIND_PARAM_INT,
    "password" => Column::BIND_PARAM_STR,
];

// Запрос роботов с параметрами, привязанными к строковым заполнителям и типам
$us = user::find(
    [
        " id = :id: AND password_ = :password:",
        "bind"      => $param,
        "bindTypes" => $types,
    ]
);
    
    $res=["success"=>false, 'mess'=>'Невірна пошта або пароль'];
    
    if(count($us)){
          $res=["success"=>true, 'mess'=>''];
        
    }
    
    echo json_encode($res);    
    
    }
);






