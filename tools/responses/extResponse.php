<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dopForma\tools\responses;
/**
 * Description of extResponse
 *
 * @author grynchuk
 */


class extResponse  
extends \Phalcon\Http\Response
implements \dopForma\interfaces\response{
    
    public $success=true,
           $data=[],
           $total=0,
           $message ;
    
    private $req,
            $resp;
           
    function __construct(\Phalcon\Http\Request $req) {
        
      parent::__construct();     

    }
    
    
    
    function send(){
        $this->setStatusCode(200, "OK");
        $this->setContent($this->formatData());
        parent::send();  
    }
    
    function formatData(){
        return json_encode(
        [
            'success'=> $this->success,
            'message'=> $this->message,
            'data'=>  $this->data ,
            'total'=>  $this->total    
        ] 
                );
    }
    
    
    function sendError($message){
        $this->success=false;
        $this->message=$message;    
        $this->send();
    }
    
}
