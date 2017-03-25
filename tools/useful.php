<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of useful
 *
 * @author grynchuk
 */
namespace dopForma\tools;

class useful {

  public static function show($d){
       echo "<pre>".print_r($d,1)."<pre>";
  } 
  
  
   public static function convToWin($d ){
       return self::conv($d,'utf-8','windows-1251');
   }
   
   public static function convToUTF8($d ){
       return self::conv($d,'windows-1251','utf-8');
   }
   
   public static function conv($d, $init='windows-1251', $target='utf-8' ){
       return iconv($init,$target, $d );
   }
   
   
   public static function sendEmail($to, $subject ,  $text){
       
   }
   
}
