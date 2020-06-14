<?php

namespace App\Model;

class ArmazenamentoModel extends \Core\Model{

    public $timestamps = true;

  public function __construct($schema = 'lab_armazenamento'){
    parent::__construct($schema);
  }

  public function storageVoid(){
    $sql = "select lab_armazenamento.id as 'id_armazenamento', sum(lab_estoque.peso) as 'calcpeso', descricao from lab_armazenamento";
    $sql .= " left join lab_estoque on lab_estoque.idarmazenamento = lab_armazenamento.id";
    $sql .= " where lab_armazenamento.idstatus = 1";
    $sql .= " group by lab_estoque.idarmazenamento, lab_armazenamento.descricao";
    $sql .= " order by lab_armazenamento.id";

    $recordset = $this->query($sql);
  
    $calcpeso = [];
    foreach ( $recordset as $value ){
      if(($value['calcpeso'] == 0)){
        $calcpeso[] = ['id' => $value['id_armazenamento'], 'descricao' => $value['descricao'] ];
      }
    }

    return $calcpeso;

  }

}