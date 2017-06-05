<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Класс для работы с пользователем
 * @property int    $id ид пользователя
 * @property int    $asp_id ид пользователя в аспДок
 * @property string $password_ хеш пароля
 * @property string $email почта
 * @property string $fio имя
 * @property string $secret ключ апи для пользователя
 * @author grynchuk
 */

namespace dopForma\models;

use Phalcon\Mvc\Model;
use Phalcon\Db\Column;

class user extends Model {

    protected $id,
              $asp_id,
              $password_,
              $email,
              $fio,
              $secret ;

    public function initialize() {
        $this->setSource("user_");
    }

    public function getSequenceName() {
        return "next_user";
    }

    function setSecret($sec=''){
        $sec=(!$sec)?$this->genSecret(30):$sec;
        //$this->fio=$sec;
        $this->secret=  password_hash($sec, PASSWORD_DEFAULT) ;
        return $sec;
    }
    
    
    function setPass($pwd='') {
       // $this->password_=  password_hash($pwd, PASSWORD_DEFAULT) ;
       $pwd=(!$pwd)?$this->genSecret():$pwd;
       $this->password_=  password_hash($pwd, PASSWORD_DEFAULT) ;
       return $pwd; 
    }

    private function genSecret($num=10){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $res='';
        $count = strlen($chars);
        for ($i = 0; $i < $num; $i++) {
            $index = rand(0, $count - 1);
            $res.= substr($chars, $index, 1);
        }
        return $res;
    }
    
    
    function setAspId($id) {
        if (filter_var($id, FILTER_VALIDATE_INT)) {
            $this->asp_id = $id;
        }
    }

    function setFio($fio){
        $this->fio=$fio;
    }
    
    function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        }
    }

    function __get($name) {
        return $this->$name;
    }

    
    /**
     * Проверяем пользователя
     * @param int $user идентификатор пользователя
     * @param string $password пароль
     * @throws \Exception  исключение если праоль не верный 
     */
    static function checkUser($user, $password, $apiKey) {
        
//        echo " $user, $password, $apiKey  ";
//        die();
        if($user and $password){
        $us=  self::getUserById($user);
        
        if ( !password_verify($password, $us->password_) ) {
           throw new \Exception ('Невірний пароль') ;
        }
         
        }elseif(    !$user 
                and !$password
                and  $apiKey
                ){
           
          $us=self::getUserByApiKey($apiKey);
          if(!$us){
              throw new \Exception ('Помилка авторизації') ;
          }
            
        }else{
            throw new \Exception ('Помилка авторизації') ;
        }
        
        return $us;
    }
    
    
    /**
     * Получить пользователя по идентификатору
     * @param string $id идентификатор пользователя
     * @return user обїект пользователя 
     * @throws \Exception если пользователь не найден
     */
    
    static function getUserById($id){
        
        $param = [
            "id" => $id        
        ];
   
        $types = [
            "id" => Column::BIND_PARAM_INT
        ];

        $us=user::findFirst(
                        [
                            " id = :id: ",
                            "bind" => $param,
                            "bindTypes" => $types,
                        ]
        );
        
        if (!$us  ) {
           throw new \Exception ('Користувача не знайдено') ;
        }

        return $us;
    }
    
    static function getUserByApiKey(  $apiKey){
        list($user
            ,$key)=explode('_',$apiKey);
        
        $us=self::getUserById($user);
        
        if(!password_verify($key, $us->secret)){
            throw new \Exception('Користувача не знайдено');
        }

        return $us;  
    }
    
    

}
