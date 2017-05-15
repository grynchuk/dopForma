<?php

use dopForma\models\exam;
use Phalcon\Db\Column;
use dopForma\models\examTypes; 
use dopForma\tools\useful;
use dopForma\tools\responses\factory as respFac;
use \dopForma\models\choice ;
// CRUD operations
$app->get(
       //  "/choice",
    "/choice",
    function () use ($app) {
  
      
        $param = [
            "userId" => $app->user->id
        ];
  
      
        $types = [
            "userId" => Column::BIND_PARAM_INT,            
        ];

//        useful::show( [
//                            " user_ = :userId: ",
//                            " bind" => $param,
//                            " bindTypes" => $types,
//                            
//                        ]);
       // die('dd');
//        
        $us = choice::find(
                        [
                            "conditions"=>  " user_ = :userId: ",
                            "bind" => $param,
                            "bindTypes" => $types,
                            "order"=> ' sort asc'                
                        ]
        );
      
        $res=[
             'exam1'=> 0
            ,'exam2'=> 0
            ,'exam3'=> 0
        ];
        
        if( count($us) and count($us)==3 ){
        $res=[
             'exam1'=> $us[0]->getExam()
            ,'exam2'=> $us[1]->getExam()
            ,'exam3'=> $us[2]->getExam()
        ];          
            
        }elseif(count($us)>3){
            throw new \Exception('Error in chice handling for user='.$userId);
        }
        
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->data=$res;
        $resp->send();
      
    }
);


$app->post(
    "/choice",
    function () use ($app) {
        
    }
);



$app->put(
    "/choice/{params}",
    function ($params) use ($app) {
      $data=$app->request->getJsonRawBody(TRUE);
      $userId=$app->user->id;
    
      foreach(['1','2','3'] as $val){
         if(!array_key_exists('exam'.$val, $data)){
             throw new \Exception('invalid params set');
         }   
        $ch=choice::getChoiceByUserAndNum( $userId, $val);
        if($ch){
            $ch->setExam($data['exam'.$val]);
        }else{
            $ch= new choice();
            $ch->setUser($userId);
            $ch->setSort((int)$val);
            $ch->setExam($data['exam'.$val]);            
        }
        $ch->setDate( new \DateTime() );
        
        
        
        if(!$ch->save()){             
             throw new \Exception(implode('<br>', $ch->getMessages()));
        }
      }
      
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->data=$data;
        $resp->send();
      
    }
);


$app->delete(
    "/choice",
    function () use ($app) {
    
    //$data=$app->request->getJsonRawBody();
    
    }
);

