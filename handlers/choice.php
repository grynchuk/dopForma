<?php

use dopForma\models\exam;
use Phalcon\Db\Column;
use dopForma\models\examTypes; 
use dopForma\tools\useful;
use dopForma\tools\responses\factory as respFac;
// CRUD operations
$app->get(
    "/choice",
    function () use ($app) {
    
//    $d=exam::find();
//    $resp=respFac::create('ext',$app->request);
//    $resp->success=true;
//    $resp->total=count($d);
//    
//    for($i=0, $l=count($d); $i<$l; $i++){
//    $resp->data[]=[
//                    'id'=>$d[$i]->id,
//                    'name'=>$d[$i]->name_
//                   ];    
//    }     
//    $resp->send();
    
    }
);


$app->post(
    "/choice",
    function () use ($app) {
    
   // $data=$app->request->getJsonRawBody();
    
    }
);



$app->put(
    "/choice",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);


$app->delete(
    "/choice",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);

