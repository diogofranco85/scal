<?php

  namespace App\Controller;
  
  use App\Model\TipoAmostraModel;
  use \App\Src\MY_Controller;
  use \Core\Database\Transaction;

  class TipoAmostrasController extends MY_Controller{

    public function __construct(){ 
      parent::__construct();
      $this->authenticate();
        
    }

    public function index()
    {
      Transaction::open('valorem');
      $tp = TipoAmostraModel::all('ativo = "S"');
      Transaction::close();

      $this->userData('grids',$tp);
      $this->userData('page_title','Tipos de Amostras');
      $this->render('tpamostras_index');
    }

    public function edit()
    {
        Transaction::open('valorem');
        $cliente = TipoAmostraModel::find($this->input->post('id'));
        $ddCliente = $cliente->toArray();
        echo json_encode($ddCliente);
        Transaction::close();
    }

    public function save()
    {
          Transaction::open('valorem');
          $id = $this->request->request->get('id');
          $usuario = $this->session->usuario;
          if($id == ''){
              $tp = new TipoAmostraModel();
              $tp->descricao = \strtoupper($this->request->request->get('descricao'));
              $tp->setor = $this->request->request->get('setor');
              $tp->created_at = date('Y-m-d h:m:s');
              $tp->created_id = $usuario['idUsuario'];
              $tp->create();

          }else{
              $tp = TipoAmostraModel::find($id);
              $tp->descricao =  \strtoupper($this->request->request->get('descricao'));
              $tp->setor = $this->request->request->get('setor');
              $tp->updated_at = date('Y-m-d h:m:s');
              $tp->updated_id = $usuario['idUsuario'];
              $tp->update();
          }
          Transaction::close();

          echo json_encode(array('retorno' => 200));
    }


  }