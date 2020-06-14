<?php

namespace App\Model;

class OperadorModel extends \Core\Model{

    public $timestamps = true;

  public function __construct($schema = 'lab_operador'){
    parent::__construct($schema);
  }

}