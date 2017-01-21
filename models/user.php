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

class user extends Model
{
    
   protected $id ,
             $asp_id,
             $password_,
             $email ; 
    
   public function initialize(){
        $this->setSource("user_");
   }
   
   public function getSequenceName()
    {
        return "next_user";
    }
   
   function setPass($pass){
       $this->password_=$pass;
   }
   
   
   function setRandPass(){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $this->password_ = ''; $i < 10; $i++) {
        $index = rand(0, $count - 1);
        $this->password_.= mb_substr($chars, $index, 1);
    }
   }
   
   
   
   function setAspId($id){
       if( filter_var($id, FILTER_VALIDATE_INT ) ){
           $this->asp_id=$id;
       }
   }
   
   function setEmail($email){
       if( filter_var($email, FILTER_VALIDATE_EMAIL ) ){
           $this->email=$email;
       }
   }
   
   function __get($name){
       return $this->$name;
   }
}

