<?php

namespace App\Models;
require_once 'Printable.php';
class BaseElement implements Printable{
  protected $title;
  public $description;
  public $visible=true;
  public $months;


   public function __construct($title,$description){
     $this->setTitle($title);
     $this->description= $description;

   }
   public function getTitle()
  {

    return $this-> title;
  }

  public function setTitle($title)
  {
    if ($title=='') {
      $this->title='N/A';
    }else {
      $this->title=$title;
    }

  }

  public function getDurationAsString(){
          $years=floor($this->months /12);
          $extraMonths=$this->months%12;
          if($years==0){
            return " $extraMonths months";
          }
          else if ($extraMonths==0){
            return "$years years ";
          }
          else{
            return "$years años $extraMonths meses";
          }
    }

    public function getDescription(){
      return $this->description;
    }

}
