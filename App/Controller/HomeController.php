<?php

  namespace App\Controller;
  
  use App\Model\EtiquetasModel;
  use \App\Src\MY_Controller;
  use \App\Model\ClientesModel;
  use \App\Model\AmostrasModel;
  use \App\Model\ContratosModel;
  use \Core\Database\Transaction2;

  class HomeController extends MY_Controller{

    public function __construct(){ 
      parent::__construct();
      $this->authenticate();
        
    }

    public function indexAction(){

      Transaction::open('valorem');
      
      $etiquetas_colunas = array(
        'lab_etiquetas' => ['id as idEtiquetas','setor'],
        'lab_cliente' => ['nmcliente'],
        'lab_contrato' => ['hibrido', 'numcontrato'],
        'lab_operador' => ['name']
      );

      $etiquetas = new EtiquetasModel();
      $etiquetas->fillable($etiquetas_colunas);
      $etiquetas->innerJoin('lab_contrato','id','idcontrato');
      $etiquetas->innerJoin('lab_cliente','id','idcliente');
      $etiquetas->innerJoin('lab_operador','id','idoperador');
      $etiquetas->limit(10);
      $etiquetas->order('lab_etiquetas.id DESC');
      

      $contratos = new ContratosModel();
      $contratos->fillable(array('lab_cliente' => ['nmcliente'], 'lab_contrato' => ['hibrido','numcontrato']));
      $contratos->innerJoin('lab_cliente','id','idcliente');
      $contratos->limit(10);
      $contratos->order('lab_contrato.id DESC');



      $this->userData('clientes', count(ClientesModel::All('ativo = "S"')));
      $this->userData('amostras', count(AmostrasModel::All('ativo = "S"')));
      $this->userData('contratos', count(ContratosModel::All('ativo = "S" AND finalizado = "N"')));
      $this->userData('etiquetas', count(EtiquetasModel::All('ativo = "S"')));
      
      $this->userData('ultimas_etiquetas', $etiquetas->get());
      $this->userData('ultimos_contratos', $contratos->get());
      

      Transaction::close();

      $this->userData('page_title','Dashboard | Home');
      $this->render('home_index');
    }

    public function logsmodel(){

      $filename = ROOT.DS.'Log'.DS.'database.html';
      $file = \file_get_contents($filename);

      $this->userData('logsmodel', $file);
      $this->render('logs_model');

    }

    public function errorsphp(){

        $filename = ROOT.DS.'php_errors.log';
        if(file_exists($filename)){
            $data = \file_get_contents($filename);

            $this->userData('phperrors',$data);
        }else{
            $data = "<h1>Arquivo de log do php não foi configurado</h1>";
            $this->userData('phperrors',$data);
        }

        $this->render('logs_php');
    }

    public function logout(){
      $this->session->logged = null;
    }

  }
