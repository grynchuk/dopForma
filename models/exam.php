<?php


/**
 * Модель для работы с дисциплинами 
 *
 * @author grynchuk
 * @property int $id  идентификатор
 * @property int $exam_id  идентификатор идентификатор на стороне АспДок
 * @property string $name_ название дисциплины 
 * @property int $exam_type тип дисциплины
 */
namespace dopForma\models;

use Phalcon\Mvc\Model;

class exam extends Model
{
    
   protected $id ,
             $exam_id,
             $name_,
             $exam_type; 
    
   public function initialize(){
        $this->setSource("exam");
   }
   
   public function getSequenceName()
    {
        return "next_exam";
    }
   /**
    * 
    * @param int $v устанавливае ид дисц из аспДок
    */
   function setExamId($v){
       $this->exam_id=$v;
   }
   
   
   function setExamType($t){
       $this->exam_type=$t;
   }
   
   function setName($v){
//       var_dump($v);
//       die('fff');
           $this->name_=$v;
   }
   
   function __get($name){
       return $this->$name;
   }
}

