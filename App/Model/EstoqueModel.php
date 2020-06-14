<?php

namespace App\Model;

class EstoqueModel extends \Core\Model{

    public $timestamps = true;

  public function __construct($schema = 'lab_estoque'){
    parent::__construct($schema);
  }

}