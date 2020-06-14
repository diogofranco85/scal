<?php

namespace Core\Html;

use Symfony\Component\HttpFoundation\Request;
use \Core\Model;

class DataTables{

  private $num_order;
  private $dir_order;
  private $column;
  private $limit;
  private $offset;
  private $draw;
  private $model;
  private $query;
  private $search;  
  private $map;

  public function __construct(Model $model, String $query, $columns = array()){

    $request = Request::createFromGlobals();

    $this->search = $request->request->get('search');
    var_dump($this->search);
    $order = $request->request->get('order');
    $this->num_order = $order[0]['column'];
    $this->dir_order = $order[0]['dir'];
    $column = $request->request->get('columns');
    $this->column = $column[$this->num_order]['data'];
    $this->limit = $request->request->get('length');
    $this->offset = $request->request->get('start');
    $this->draw = $request->request->get('draw');

    $this->model = $model;
    $this->query = $query;

    $this->map = $this->mapCol($columns);

  }
  
  private function mapCol($column){
    $r = '';
    foreach($column as $key => $value){
      if($value['method'] == 'like'){
         $r .= "{$key} like '%vsql% OR ";
      }else{
        $r .= "{$key} = 'vsql' OR";
      }
    }
    $r = rtrim($r, ' OR');
    return $r;
  }

  private function getSQL()
  {
    $conditions = $this->map;
    $conditions = str_replace('vsql',$this->search,$conditions);

    $orderby = "ORDER BY {$this->column} {$this->dir_order}";
    $where = "{$this->query}";

    $post = ($this->search['value'] != '') ?  $this->search['value'] : '';
    $search = "{$where} AND {$conditions} {$orderby} LIMIT {$this->offset},{$this->limit}";

    __d($search);
  }

  public function addColumn($name, $html)
  {
      
      

  }

  public function draw(){

    $this->getSQL();

    echo json_encode(array(
      'draw' => $this->draw, 
      'data' => $clientes,
      'recordsFiltered' => count($amostras),
      'recordsTotal' => count(AmostrasModel::all()),
    ));

  }

}