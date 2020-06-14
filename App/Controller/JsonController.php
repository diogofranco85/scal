<?php

namespace App\Controller;

use App\Model\ClientesModel;
use \Core\Database\Transaction;

class JsonController extends \App\Src\MY_Controller
{

  public function gridCliente()
  {
    Transaction::open('valorem');
    
    $post = ($this->request->request->get('search'));
    $order = ($this->request->request->get('order'));
    $num_order = $order[0]['column'];
    $dir_order = $order[0]['dir'];
    $column = ($this->request->request->get('columns'));
    $setColumn = $column[$num_order]['data'];
    $limit = ($this->request->request->get('length'));
    $offset = ($this->request->request->get('start'));

    $orderby = "ORDER BY  {$setColumn} {$dir_order}";
    
    $search = "ativo = 'S' ";
    
    if($post['value'] != ''){
      $post = $post['value'];
      $search .= "AND numcliente = {$post} OR nmcliente like '%{$post}%' OR id = '{$post}' OR codprotheus='{$post}' ";
     
    }

    $search .= " {$orderby} LIMIT {$offset},{$limit}"; 
    $clientes  = ClientesModel::all($search);

    $datagrid = array();
    foreach( $clientes as $value){

      $btn =  "<a href='#' post_id='".$value['id']."' class='btn btn-sm btn-info btnedit'><i class='fa fa-edit'></i></a>";
      $btn .= " \n <a href='#' post_id='".$value['id']."' class='btn btn-sm btn-danger btndel'><i class='fa fa-trash'></i></a>";

     $datagrid[] = array(
       'id' => $value['id'], 
       'nmcliente' => $value['nmcliente'], 
       'numcliente' => $value['numcliente'],
       'codprotheus' => $value['codprotheus'],
       'actions' => $btn,
     );
    }

    $json = array(
          'draw' => $this->request->request->get('draw'), 
          'data' => $datagrid,
          'recordsFiltered' => count($clientes),
          'recordsTotal' => count(ClientesModel::all()),
          'sql' => $search,
      );
    echo json_encode($json);

    Transaction::close();
  }

  public function gridAmostras()
  {
    Transaction::open('valorem');
    
    $colunas = array(
      'numcliente' => array(
        'type' => 'int',
        'method' => 'like',
      ),
      'nmcliente' => array(
        'type' => 'char',
        'method' => 'like',
      ),
      'id' => array(
        'type' => 'int',
        'method' => '=',
      ),
    );

    $grid = new \Core\Html\DataTables(new ClientesModel, 'ativo = "S"', $colunas);
    $grid->draw();
    
    Transaction::close();
  }

}