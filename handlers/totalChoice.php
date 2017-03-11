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
    "/totalChoice",
    function () use ($app) {
     
    
        $sql="select e.id as exam,  et.max_number as maxNum, et.min_number as minNum , count(c.user_) as num
from exam e 
left join choice c on e.id=c.exam 
, exam_types et
where
 et.id=e.exam_type
group by e.id , et.max_number, et.min_number";
        
        $r=$app->request;
        $page=$r->get('page');
        $start=$r->get('start');
        $limit=$r->get('limit');        
        $props=[ 'exam',  'num', 'minNum','maxNum'];
        
        $resultset=$app->db->query($sql); 
        $res=$resultset->fetchAll();
        $data=[];
//        useful::show($res);
//        die(" $start -- ".($start + $limit)."  ");
        $totalCount=count($res);
        for(   
             $l = ($totalCount<$start + $limit)?$totalCount : $start + $limit 
            , $k=0
            , $ll=count($props)   
            , $i=$start;
              $i<$l;
              $i++, $k++){
                $data[$k]=[];
              for($j=0;$j<$ll; $j++){
                   $data[$k][$props[$j]]=$res[$i][strtolower($props[$j])] ;
              }
        }
        
    
        $r=$app->request;
             
        $resp=respFac::create('ext',$app->request);
        $resp->success=true;
        $resp->total=$totalCount;
        $resp->data=$data;
        $resp->send();
      
    }
);







