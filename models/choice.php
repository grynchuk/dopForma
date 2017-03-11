<?php

/**
 * Description of choice
 *
 * @author grynchuk
 */
namespace dopForma\models;

use Phalcon\Mvc\Model;
use Phalcon\Db\Column;
class choice extends Model
{
    
   protected $id ,
             $user_,
             $exam,
             $sort; 
    
   public function initialize(){
        $this->setSource("choice");
   }
   
   public function getSequenceName()
    {
        return "next_choice";
    }
   
   function setExam($v){
       $this->exam=$v;
   }
   
   function setUser($v){
       $this->user_=$v;
   }
   
   function setSort($v){
       $this->sort=$v;
   }   
   
   function getExam(){
      return $this->exam;
   }
   
   function getUser(){
      return $this->user_;
   }
   
   function getSort(){
       return $this->sort;
   }
      
   static function getChoiceByUserAndNum($user, $num){
        $param = [
            "user_" => $user,
            "sort" => $num,
        ];
  
//        \dopForma\tools\useful::show($param);
//        die('ff');
        $types = [
            "user_" => Column::BIND_PARAM_INT,
            "sort"  => Column::BIND_PARAM_INT,
        ];

        $us = self::find(
                        [
                            " user_ = :user_: AND sort = :sort:",
                            "bind" => $param,
                            "bindTypes" => $types,
                        ]
        );
//die('vv');
        //$res = ["success" => false, 'mess' => 'Невірна пошта або пароль'];
       return (count($us))? $us[0] : null ;
        
   }
   
}

