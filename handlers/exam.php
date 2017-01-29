<?php
use dopForma\models\exam;
use Phalcon\Db\Column;
use dopForma\models\examTypes; 
use dopForma\tools\useful;
use dopForma\tools\responses\factory as respFac;
// CRUD operations
$app->get(
    "/exam",
    function () use ($app) {
    
    $d=exam::find();
    $resp=respFac::create('ext',$app->request);
    $resp->success=true;
    $resp->total=count($d);
    
    for($i=0, $l=count($d); $i<$l; $i++){
    $resp->data[]=[
                    'id'=>$d[$i]->id,
                    'name'=>$d[$i]->name_
                   ];    
    }     
    $resp->send();
    
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


$app->post('/exams/types',
        function () use($app){
          
        $d=$app->request->get('data');
        $d=json_decode($d, true);
    //    useful::show($d);
//        echo "<pre>".print_r($d,1)."</pre>";
//        die();
        $res=['success'=>true,
              'data'=>''
             ]; 
        
        for($i=0, $l=count($d); $i<$l; $i++){
            $u= new examTypes();            
            
          
            $u->setName($d[$i]['name_']);
            $u->setExamTypeId( $d[$i]['exam_type_id'] );
            $u->setMaxNumber(  $d[$i]['max_number'] );
            $u->setMinNumber(  $d[$i]['min_number'] );            
            if(!$u->create()){
               $res["success"]=false;
               $res['data']=  implode(',', $u->getMessages());
            }
         }   
          
          echo json_encode($res);
        }
        );

$app->post(
    "/exams",
    function () use ($app) {
    
    $data=$app->request->get('data');
    $data=json_decode($data, true);
//    useful::show($data);
//    die('ddddd');
    $errors=[];
    foreach( $data as  $val ){
        
        
//        echo " {$val['val']} <br> ";
//        continue;
        if(!$val['val']) continue;
        $val['val']=str_replace("'","", $val['val']);
        $u=exam::find(" name_='{$val['val']}' " );
        if(count($u)) continue;
        $e=new exam();
        $e->setExamType($val['exam_type']);
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






