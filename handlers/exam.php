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

/**
 * Создание нового экзамена 
 */

$app->post(
    "/exam",
    function () use ($app) {
    $errors='';
    $req=$app->request;
    $data=json_decode($req->get('data'),1);
    
    $exam= new exam();
    $exam->setExamId($data['exam_id']);
    $exam->setName(useful::convToUTF8($data['name_']));
    if($data['exam_type_id']>0){
       $type= examTypes::findFirst([
          " exam_type_id = {$data['exam_type_id']} " 
       ]);
       if($type){
                   $exam->setExamType($type->id);
       }     
    }
    
    if(!$exam->create()){
              $errors=' Error '.implode(',', $exam->getMessages()) ;        
    }
    
        $resp=respFac::create('ext',$app->request);
        $resp->success=($errors)?false:true;
        $resp->data = ($errors)?:'ok'; 
        $resp->send();  
      
    
    }
);


/** 
 * Обновление экзамена
 */
$app->put(
    "/exam",
    function () use ($app) {
     
    $errors='';
    $req=$app->request;
    $data=$app->request->getJsonRawBody(TRUE);
//    useful::show($data);
//    die('dd');
    $data['exam_id']=(int)$data['exam_id'];
    if(!($data['exam_id']>0)){
        $errors='Не корректный exam_id';
    }else{
    //$exam= examTypes::findFirst(" exam_id = {$data['exam_id']} ");
    $exam= exam::findFirst(" exam_id = {$data['exam_id']}");
    $examType= examTypes::findFirst(" exam_type_id = {$data['exam_type_id']}");
    if($exam and $examType){
        $exam->setName($data['name_']);
        $exam->setExamType($examType->id);
//        var_dump($exam->save());
//        die();
       if(!$exam->save()){
              $errors=' Error '.implode(',', $exam->getMessages()) ;        
       }
    }
    
    }
    
        $resp=respFac::create('ext',$app->request);
        $resp->success=($errors)?false:true;
        $resp->message = ($errors)?:'ok'; 
        $resp->send();  
     
    
    }
);


$app->delete(
    "/exam",
    function () use ($app) {
     $id=$app->request->get('exam_id');
     $error='';
     if($id>0){
     $exam=  exam::findFirst(" exam_id = {$id} "); 
     if($exam){
         if(!$exam->delete()){
            
           $error=implode(',', $exam->getMessages());    
         }
     }else{
      //    die('fffff');
         $error= 'Запись не найдена';
     }
         
     }else{
      $error='Не корректный ид'  ;  
     }
        $resp=respFac::create('ext',$app->request);
        $resp->success=($error)? false : true ;
        $resp->message=($error)?: 'ok';
        $resp->send();  
     
    }
);

//



// list operations

// типы  экзаменов
/**
 * Добавляет тип экзамена
 */
$app->post('/exams/types',
        function () use($app){
          
        $d=$app->request->get('data');
        $d=json_decode($d, true);
        if(array_key_exists('exam_type_id', $d )){
            $d=[$d];
        }
//        useful::show($d);
//        die('dd');
        $errors='';
        for($i=0, $l=count($d); $i<$l; $i++){
            $u= new examTypes();            
            $u->setName($d[$i]['name_']);
            $u->setExamTypeId( $d[$i]['exam_type_id'] );
            $u->setMaxNumber(  $d[$i]['max_number'] );
            $u->setMinNumber(  $d[$i]['min_number'] );            
            if(!$u->create()){
              $errors=  implode(',', $u->getMessages());
            }
         }   
          
        //  echo json_encode($res);
          
        $resp=respFac::create('ext',$app->request);
        $resp->success=($errors)?false:true;
        $resp->message=$errors;
        //$resp->total=count($data);
        $resp->data=($errors)?:'ok' ;
        $resp->send();
          
        }
        );
/**
 * Обновляет тип экзамена
 */
$app->put('/exams/types',
        function () use($app){
         
    $errors='';
    
    $data=$app->request->getJsonRawBody(TRUE);
//    useful::show($data);
//    die('dd');
    $data['exam_type_id']=(int)$data['exam_type_id'];
    if(!($data['exam_type_id']>0)){
        $errors='Не корректный exam_type_id';
    }else{
    $type= examTypes::findFirst(" exam_type_id = {$data['exam_type_id']}");
    if($type){
        $type->setName($data['name_']);
        $type->setMinNumber($data['min_number']);
        $type->setMaxNumber($data['max_number']);
       if(!$type->save()){
              $errors=' Error '.implode(',', $type->getMessages()) ;        
       }
       }else{
              $errors='Exam type not found';
       }
        }
        
        $resp=respFac::create('ext',$app->request);
        $resp->success=($errors)?false:true;
        $resp->message=$errors;
        //$resp->total=count($data);
        $resp->data=($errors)?$type:'' ;
        $resp->send();  
        
        }
        );        
 /**
  * Удаляет тип экзамена
  */       
$app->delete('/exams/types',
        function () use($app){
         
         $id=$app->request->get('exam_type_id');
     $error='';
     if($id>0){
     $examType= examTypes::findFirst(" exam_type_id = {$id} "); 
     if($examType){
         if(!$examType->delete()){
           $error=implode(',', $exam->getMessages());    
         }
     }else{
      //    die('fffff');
         $error= 'Запись не найдена';
     }
         
     }else{
      $error='Не корректный ид'  ;  
     }
        $resp=respFac::create('ext',$app->request);
        $resp->success=($error)? false : true ;
        $resp->message=($error)?: 'ok';
        $resp->send();  
     }
        );        
////////        
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




