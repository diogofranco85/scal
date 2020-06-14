<?php

namespace App\Model;

class ContratosModel extends \Core\Model{

  public function __construct($schema = 'lab_contrato'){
    parent::__construct($schema);
  }

  public function list(){
    $colunas = [
      'lab_contrato' => ['id', 'hibrido', 'numcontrato', 'finalizado','ativo'],
      'lab_cliente' => ['id as "idcliente"', 'nmcliente'],
      'lab_safra' => ['descricao']
    ];
      $this->fillable($colunas);
      $this->where('lab_contrato.ativo','=','S');
      //$this->where('lab_contrato.finalizado','=','N');
      $this->innerJoin('lab_cliente','id','idcliente');
      $this->innerJoin('lab_safra','id','idsafra');
      $rs_contratos = $this->get();

      return $rs_contratos;
  }

}