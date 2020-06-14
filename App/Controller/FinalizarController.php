<?php

namespace App\Controller;

use \App\Model\ContratosModel;
use \App\Model\AmostrasModel;
use \Core\Database\Transaction;

class FinalizarController extends \App\Src\MY_Controller{

  public function __construct(){
      parent::__construct();
      $this->authenticate();
  }

  public function index(){
    Transaction::open('valorem');
    $contratos = new ContratosModel();
    $this->userData('page_title','Finalizar Contratos');
    $this->userData('contratos', $contratos->list());
    $this->render('finalizar_index');
    Transaction::close();
  }

  public function delete($id){
   
    if( $this->session->usuario['tipo']  != 2){
      $this->jsonResponse(['status' => 400, 'message' => 'Usuário não possui permissão para finalizar contrato.']);
      return;
    }

    Transaction::open('valorem');
    $contratos = ContratosModel::find($id);

    $amostras = AmostrasModel::All("idcontrato = {$id}");
    if(count($amostras) < 7){
        $this->jsonResponse(['status' => 404, 'message' => 'Contrato com menos de 7 amostras cadastradas. Informar justificativa.']);
    }else{

      if(\is_object($contratos)){
        $contratos->finalizado = 'S';
        $contratos->update();
  
        $this->jsonResponse(['status' => 200, 'message' => 'Contrato finalizado']);
      }else{
        $this->jsonResponse(['status' => 400, 'message' => 'Error ao finalizar contrato.']);

      };

    }       
    
    Transaction::close();
  } 

  public function close($id){
    
    Transaction::open('valorem');
    $contratos = ContratosModel::find($id);
      if(\is_object($contratos)){
        $contratos->finalizado = 'S';
        $contratos->justificativa = $this->input->get('msg');
        $contratos->update();
  
        $this->jsonResponse(['status' => 200, 'message' => 'Contrato finalizado']);
      }else{
        $this->jsonResponse(['status' => 400, 'message' => 'Error ao finalizar contrato.']);

      };  
    
    Transaction::close();
  }

  public function reopen($id){
    Transaction::open('valorem');

    if( $this->session->usuario['tipo']  != 2){
      $this->jsonResponse(['status' => 400, 'message' => 'Usuário não possui permissão para reabrir contrato.']);
      return;
    }


    $contratos = ContratosModel::find($id);
      if(\is_object($contratos)){
        $contratos->finalizado = 'N';
        $contratos->update();
  
        $this->jsonResponse(['status' => 200, 'message' => 'Contrato Reaberto']);
      }else{
        $this->jsonResponse(['status' => 400, 'message' => 'Error ao finalizar contrato.']);

      };  
    
    Transaction::close();
  }

}