<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author grynchuk
 */
namespace dopForma\models;

use Phalcon\Mvc\Model;

class examTypes extends Model
{
    
   protected $id ,
             $exam_type_id,
             $name_,
             $max_number,
             $min_number
           ; 
    
   public function initialize(){
        $this->setSource("exam_types");
   }
   
   public function getSequenceName()
    {
        return "next_exam_type";
    }
   
   function setExamTypeId($v){
       $this->exam_type_id=$v;
   }
   
   
   function setMaxNumber($n){
       $this->max_number=$n;
   }
   
   
   function setMinNumber($n){
       $this->min_number=$n;
   }
   
   function setName($v){
           $this->name_=$v;
   }
   
   function __get($name){
       return $this->$name;
   }
}

