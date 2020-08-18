<?php

namespace App\Controller\API;

use \Core\Database\Transaction;

class Clientes extends \Core\Controller{

    public function index(){
        try{
            Transaction::open('valorem');
                $cliente = new \App\Model\ClientesModel();
                $cliente->where('ativo','=','S');
                
                $this->jsonResponse($cliente->get(), 200);
            Transaction::close();
        }catch(Exception $e){
            $this->jsonResponse(['message' => $e->getMessage], 500);
        }
    }


}
