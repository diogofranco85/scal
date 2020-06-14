<?php

namespace App\Model;

class EtiquetasModel extends \Core\Model{

    public $timestamps = true;

  public function __construct($schema = 'lab_etiquetas'){
    parent::__construct($schema);
  }

  public function etiqueta_index(){

      $colunas = array(
          'lab_cliente' => array(
              'id as "idcliente"', 'nmcliente', 'descricao'
          ),
          'lab_contrato' => array(
              'id as "idcontrato"', 'numcontrato','hibrido'
          ),
          'lab_etiquetas' => array('ativo')
      );

      $this->fillable($colunas);
      $this->where('lab_etiquetas.ativo', '=','S');
      $this->innerJoin('lab_cliente','id','idcliente');
      $this->innerJoin('lab_contrato','id','idcontrato ');
      $this->group('lab_contrato.id, lab_contrato.hibrido');
      return $this->get();

  }

}