<?php
use dopForma\models\exam;
use Phalcon\Db\Column;
use dopForma\models\examTypes; 
use dopForma\models\choice;
use dopForma\models\user;
use dopForma\tools\useful;
use dopForma\tools\responses\factory as respFac;
// CRUD operations
$app->get(
    "/exam",
    function () use ($app) {
   
    $sql="
select e.id as id,  e.name_ as name_
       --, et.max_number, et.min_number , ch.num
from exam e 
     left join 
     ( 
     select  exam, count(id) as num from choice group by exam
     ) ch on e.id=ch.exam,
     exam_types et 
      
where e.exam_type=et.id 
     and (
          ( et.min_number>0 and et.max_number>0
            and  (ch.num < et.max_number) or ch.num is null  )
           or
           (et.min_number=0 and et.max_number=0)
         ) ";
   
     $resultset=$app->db->query($sql); 
     $res=$resultset->fetchAll();
    
    $resp=respFac::create('ext',$app->request);
    $resp->success=true;
    $resp->total=count($res);
    
    for($i=0, $l=count($res); $i<$l; $i++){
    $resp->data[]=[
                    'id'=>$res[$i]['id'],
                    'name'=>$res[$i]['name_']
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

$app->get('/exam/choice',       
        function() use($app){
           $exam=$app->request->get('exam');
           if( !($exam>0) ) throw new \Exception ('Не корректный екзамен');
           
            $sql="select u.fio as fio
                    from  choice c 
                        , user_ u
                        , exam  e  
                    where c.exam=$exam
                      and u.id=c.user_
                      and c.exam=e.id";
        
        $r=$app->request;
        $page=$r->get('page');
        $start=$r->get('start');
        $limit=$r->get('limit');        
        
        $resultset=$app->db->query($sql); 
        $res=$resultset->fetchAll();
         $data=[];  
         
        //  var_dump($res);
        for($i=0,$l=count($res);
            $i<$l;
            $i++        
           ){
             $data[]= ['fio'=> $res[$i]['fio'] ]             ;
           }   
         
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->total=count($data);
        $resp->data=$data;
        $resp->send();  
           
           
        }        
        );




