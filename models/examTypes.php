<?php



/**
 * Модель для работы с типами дисциплин
 * @property int $id идентификатор типа 
 * @property int $exam_type_id идентификатор типа на стороне аспДок
 * @property string $name_ имя типа
 * @property int $max_number Максимальное количество человек
 * @property int $min_number Минимальное количество чел
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
   /**
    * 
    * @return string название последотовательности
    */
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

