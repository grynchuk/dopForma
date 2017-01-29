<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace dopForma\tools\responses;

/**
 * Description of factory
 *
 * @author grynchuk
 */
class factory {
    
//    private $req ;
//    
//    function __construct(Phalcon\Http\Request $req) {
//     $this->req=$req;
//    }
    
    
   public static function create($inst, \Phalcon\Http\Request $req ){
       
        $alias=[
            'ext'=>'\dopForma\tools\responses\extResponse'
        ];
       
        if(array_key_exists($inst, $alias)){
            $inst=$alias[$inst];
        }
        $obj= new $inst($req);        
        return $obj;
    }
    
}
