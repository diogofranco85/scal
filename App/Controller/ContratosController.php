<?php

  namespace App\Controller;
  
  use App\Model\AmostrasModel;
  use App\Model\ClientesModel;
  use App\Model\ContratosModel;
  use App\Model\SafraModel;
  use \App\Src\MY_Controller;
  use \Core\Database\Transaction;

  class ContratosController extends MY_Controller{

    public function __construct(){ 
      parent::__construct();
      $this->authenticate();
        
    }

    public function index($id){

      Transaction::open('valorem');

      $contratos = new ContratosModel();

      $colunas = [
          'lab_contrato' => ['id', 'hibrido', 'numcontrato', 'finalizado','ativo'],
          'lab_cliente' => ['id as "idcliente"', 'nmcliente'],
          'lab_safra' => ['descricao']
        ];

      $contratos->fillable($colunas);
      $contratos->innerJoin('lab_cliente','id','idcliente');
      $contratos->innerJoin('lab_safra','id','idsafra');
      $contratos->where('lab_cliente.id','=',$id);
      $contratos->where('lab_contrato.ativo','=','S');
      $contratos->where('lab_contrato.finalizado','=','N');
      $rs = $contratos->get();

      $cliente = ClientesModel::find($id,'id, nmcliente');

      $safra = SafraModel::all('ativo = "S" AND finalizado = "N"');

      $url_back = $this->getURL('cliente');


      $this->userData('page_title','Contratos &raquo; <strong>'. $cliente->nmcliente.'</strong>');
      $this->userData('safras',$safra);
      $this->userData('contratos',$rs);
      $this->userData('codcliente', $cliente->id);
      $this->userData('nmcliente',$cliente->nmcliente);
      
      $this->render('contratos_index');

      Transaction::close();
    }

    public function save(){
        Transaction::open('valorem');
        $id = $this->request->request->get('id');
        if( $id == ''){
            $contrato = new ContratosModel();
            $contrato->hibrido = $this->request->request->get('hibrido');
            $contrato->idcliente = $this->request->request->get('idcliente');
            $contrato->numcontrato = $this->request->request->get('numcontrato');
            $contrato->idsafra = $this->request->request->get('idsafra');
            $contrato->create();
        }else{
            $contrato = ContratosModel::find($id);
            $contrato->hibrido = $this->request->request->get('hibrido');
            $contrato->idcliente = $this->request->request->get('idcliente');
            $contrato->numcontrato = $this->request->request->get('numcontrato');
            $contrato->idsafra = $this->request->request->get('idsafra');
            $contrato->update();

        }

        echo json_encode(array('retorno' => 200));

        Transaction::close();
    }

      public function edit()
      {
          Transaction::open('valorem');

          $id = $this->request->request->get('id');
          $contrato = ContratosModel::find($id);
          $ddContrato = $contrato->toArray();
          echo json_encode($ddContrato);

          Transaction::close();

      }

      public function delete(){

        Transaction::open('valorem');
        $id = $this->request->request->get('id');
        $amostra = new AmostrasModel();
        $amostra->where('idcontrato','=', $id);
        $rs = $amostra->get();

        if(count($rs)){
            echo json_encode(array('code' => 1));
        }else{
            echo json_encode(array('code' => 0));
        }

        Transaction::close();

      }

  }