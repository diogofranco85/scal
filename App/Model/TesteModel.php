<?php

namespace App\Model;

class TesteModel extends \Core\Model
{

    public $timestamps = true;

  public function __construct($schema = 'lab_teste'){
    parent::__construct($schema);
  }

}