<?php

namespace App\Model;

class AmostrasModel extends \Core\Model{

  public function __construct($schema = 'lab_amostra'){
    parent::__construct($schema);
  }

  public function query_index(){

    $colunas =  'lab_amostra.id, lab_amostra.protocolo, lab_amostra.dtamostra, lab_amostra.peso, lab_amostra.observacao, lab_tipoamostra.descricao as "TPDesc", lab_cliente.nmcliente,  lab_amostra.created_at as "amostra_created_at", ';
    $colunas .= ' lab_contrato.hibrido, lab_contrato.numcontrato, lab_safra.descricao as "safra"';
    $colunas .= ' ,lab_operador.name, lab_etiquetas.id as idetiqueta, lab_etiquetas.setor';

    $this->fillable($colunas);
    $this->innerJoin('lab_contrato', 'id','idcontrato');
    $this->innerJoin('lab_etiquetas', 'id','numetiqueta');
    $this->innerJoin('lab_cliente','id','idcliente','lab_contrato');
    $this->innerJoin('lab_safra','id','idsafra','lab_contrato');
    $this->innerJoin('lab_tipoamostra','id', 'idtipoamostra');
    $this->innerJoin('lab_operador','id', 'idoperador','lab_amostra');
    $this->where('lab_amostra.ativo','=','S');
    $this->where('lab_contrato.finalizado','=','N');

    return $this->get();

  }

}