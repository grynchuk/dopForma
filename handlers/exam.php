<?php
use dopForma\models\exam;
use Phalcon\Db\Column;

// CRUD operations
$app->get(
    "/exam",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);


$app->post(
    "/exam",
    function () use ($app) {
    
   // $data=$app->request->getJsonRawBody();
    
    }
);



$app->put(
    "/exam",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);


$app->delete(
    "/exam",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);


// list operations




$app->post(
    "/exams",
    function () use ($app) {
    
    $data=$app->request->get('data');
    
    $errors=[];
    foreach( $data as  $val ){
//        echo " {$val['val']} <br> ";
//        continue;
        if(!$val['val']) continue;
        $val['val']=str_replace("'","", $val['val']);
        $u=exam::find(" name_='{$val['val']}' " );
        if(count($u)) continue;
        $e=new exam();
        $e->setExamId($val['item']);
        $e->setName($val['val']);
        if(!$e->create()){
              $errors[]=' Error '.implode(',', $e->getMessages()) ;        
        }
    }
    
    $resp=['success'=>true,
           'data'   => []
          ];
    
    if(count($errors)){
        $resp=['success'=>false,
               'data'   => $errors
          ];
    }
    
    echo json_encode($resp);
    
      
    }
);


$app->get(
    "/exams",
    function () use ($app) {
    die('fffff');
    echo "<pre>".print_r($app->request,1)."<pre>";
    }
);






