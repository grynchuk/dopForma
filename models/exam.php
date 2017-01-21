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

class exam extends Model
{
    
   protected $id ,
             $exam_id,
             $name_ ; 
    
   public function initialize(){
        $this->setSource("exam");
   }
   
   public function getSequenceName()
    {
        return "next_exam";
    }
   
   function setExamId($v){
       $this->exam_id=$v;
   }
   
   
   function setName($v){
           $this->name_=$v;
   }
   
   function __get($name){
       return $this->$name;
   }
}

