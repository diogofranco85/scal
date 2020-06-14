<?php

namespace App\Controller;

use Core\Database\Transaction;

class StatusController extends \App\Src\MY_Controller{

  public function __construct(){
    parent::__construct();
    $this->authenticate();
  }

  public function stEnderecamento(){

      Transaction::open('valorem');
        
      Transaction::close();

  }

}