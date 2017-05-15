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
     
       $searchHandler=[
           
           'exam'=> function($val){
             return " e.name_ like '%$val%' ";
           }
       ];
       $searchCond=[];
       
       $sortHandler=[
           'exam'=> function($order){
             return ' e.name_ '.$order;
           },
           'num'=> function($order){
             return ' count(c.user_) '.$order;
           },        
           'minNum'=>function($order){
               return ' et.min_number  '.$order;
           },        
           'maxNum'=>function($order){
               return ' et.max_number  '.$order;
           }        
                   
       ];
       
        $getExam=function($r, $name){
            $res='';
            if(array_key_exists($name, $_REQUEST)){
                $res=$r->get($name);
            }
            return $res;
        };
         $r=$app->request;
        
        $exams=[];
        
        foreach(['exam1',
                'exam2',
                'exam3'] as $val){
           if( $ex=$getExam($r,$val)){
           $exams[]=$ex;    
           }
        }
        
        $page=$r->get('page');
        $start=$r->get('start');
        $limit=$r->get('limit');        
        $sort=$r->get('sort');
        $filter=$r->get('filter');
        $props=[ 'exam',  'num', 'minNum','maxNum'];
        if($sort){
            $sort=json_decode($sort,1)[0];
           // useful::show($sort);
        }
        
        if($filter){
            $filter=json_decode($filter,1);      
            $filter=array_reduce($filter, function($car, $item) use($searchHandler){
               return  $car. ' and '.$searchHandler[$item['property']]($item['value']);
            });
        }else{
            $filter='';
        }
        
        if($exams){
            $filter.= ' and e.id in ('.implode(',',$exams).') ';
        }
        
        
        $sql="select e.id as exam,  et.max_number as maxNum, et.min_number as minNum , count(c.user_) as num
from exam e 
left join choice c on e.id=c.exam  and  to_char( now()  ,'YYYY')=to_char(ch_date ,'YYYY')
, exam_types et
where
 et.id=e.exam_type
 ".$filter."
group by e.id , et.max_number, et.min_number
".(                                            
           ($sort and array_key_exists( $sort['property'], $sortHandler ))
                ?
                ' order by '.$sortHandler[$sort['property']]($sort['direction'])
                :
                ''
                )
               ;
        //throw new \Exception($sql);
        
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







