<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Класс для получения новых паролей
 *
 * @author grynchuk
 * @property int $code уникальный код подтверждения
 * @property int $user_ код пользователя
 * @property string $pass пароль пользователя
 * @property string $sec  токен
 */

namespace dopForma\models;

use Phalcon\Mvc\Model;
use Phalcon\Db\Column;

class passRestore extends Model {

    protected $code,
              $user_,
              $pass,
              $sec   
            
              ;

    public function initialize() {
        $this->setSource("pass_restore");
    }


     function setSec($sec){
         $this->sec=$sec;
         return $this;
     }
    
    function setPass($pass) {
        $this->pass= $pass;
        return $this;
    }

    function setCode() {
       $this->code=time();
       return $this;
    }

    
    function setUser($user){
        $this->user_=$user;
        return $this;
    }
    

    function __get($name) {
        return $this->$name;
    }
}

