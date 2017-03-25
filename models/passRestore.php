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
use Phalcon\Db\Column;

class passRestore extends Model {

    protected $code,
              $user_,
              $pass
              ;

    public function initialize() {
        $this->setSource("pass_restore");
    }


    function setPass($pass) {
        $this->pass= $pass;
    }

    function setCode() {
       $this->code=time();
    }

    
    function setUser($user){
        $this->user_=$user;
    }
    

    function __get($name) {
        return $this->$name;
    }
}

