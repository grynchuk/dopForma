<?php

/**
 * Description of choice
 *
 * @author grynchuk
 */
namespace dopForma\models;

use Phalcon\Mvc\Model;

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
   
   function __get($name){
       return $this->$name;
   }
}

