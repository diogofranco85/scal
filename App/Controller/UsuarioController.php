<?php

namespace App\Controller;

use Core\Controller;

class UsuarioController extends Controller
{

  public function logout(){
    $_SESSION = array();
    \header('Location: /sim');
  }

}