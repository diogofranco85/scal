<?php

namespace App\Controller;

use App\Model\ClientesModel;
use App\Model\ContratosModel;
use App\Model\EtiquetasModel;
use App\Model\TesteModel;
use App\Model\TipoAmostraModel;
use \App\Src\MY_Controller;
use \App\Model\AmostrasModel;
use \App\Model\OperadorModel;
use \Core\Database\Transaction;

class AmostrasController extends MY_Controller
{
    public function __construct(){
      parent::__construct();
      $this->authenticate();
    }

    public function index(){

      Transaction::open('valorem');
       $amostras = new AmostrasModel();
      Transaction::close();
     
      $this->userData('grids', $amostras->query_index());
      $this->userData('page_title','Amostras');

      $this->render('amostras_index');
    }

    public function loadfields(){
        Transaction::open('valorem');

        $codebar = $this->request->request->get('idetiqueta');
        $rs_amostra = new AmostrasModel();
        $rs_amostra->where('ativo','=','S');
        $rs_amostra->where('numetiqueta','=',$codebar);

        $amostra = $rs_amostra->get();

        if(count($amostra) != 0){
           $icon = "<i class='fa fa-check fa-fw'></i>";
           echo "<div class='alert alert-danger text-center'>{$icon} JÁ EXISTE AMOSTRA CADASTRADA</div>";
           return;
        }

        $columns = [
            'lab_etiquetas' => ['id as idetiqueta','idcliente','setor','idcontrato','ativo'],
            'lab_contrato' => ['finalizado'],
        ];
    
        $rs_etiqueta = new EtiquetasModel();
        $rs_etiqueta->fillable($columns);
        $rs_etiqueta->innerJoin('lab_contrato','id','idcontrato');
        $rs_etiqueta->where('lab_etiquetas.ativo','=','S');
        $rs_etiqueta->where('lab_etiquetas.id','=',$codebar);

        $etiqueta = $rs_etiqueta->get();           

        if(count($etiqueta) == 0){
            $icon = "<i class='fa fa-crop fa-fw'></i>";
            echo "<div class='alert alert-danger text-center'>{$icon} NÃO EXISTE ETIQUETA ATIVA</div>";
            return;
        }

        if($etiqueta[0]['finalizado'] == 'S'){
            $icon = "<i class='fa fa-ban fa-fw'></i>";
            echo "<div class='alert alert-warning text-center'>{$icon} CONTRATO FINALIZADO</div>";
            return;
        }


        $clientesModel = ClientesModel::find($etiqueta[0]['idcliente']);
        $contratoModel = ContratosModel::find($etiqueta[0]['idcontrato']);
        $taModel = new TipoAmostraModel();
        $taModel->where('setor', '=', $etiqueta[0]['setor']);

        $arrCliente = ['id' => $clientesModel->id, 'nome' => $clientesModel->nmcliente ];
        $arrContrato = ['id' => $contratoModel->id, 'hibrido' => $contratoModel->hibrido, 'num' => $contratoModel->numcontrato];

        $this->userData('cliente', $arrCliente);
        $this->userData('contrato', $arrContrato);
        $this->userData('idetiqueta', $codebar);
        $this->userData('amostra',$amostra);
        $this->userData('tipoAmostras', $taModel->get());
        $this->render('amostras_loadfields');

        Transaction::close();
    }

    public function editar()
    {

      Transaction::open('valorem');
      $cliente = ClientesModel::find($this->input->post('id'));
      $ddCliente = $cliente->toArray();
      echo json_encode($ddCliente);
      Transaction::close();

    }

    public function save()
    {
        Transaction::open('valorem');
        $id = $this->request->request->get('id');

        $operador = new OperadorModel();
        $operador->where('cod','=',$this->request->request->get('idoperador'));
        $rs_operador = $operador->get();

        $operEntrega = new OperadorModel();
        $operEntrega->where('cod','=',$this->request->request->get('entidoperador'));
        $rs_operEntrega = $operEntrega->get();

        if($id == ''){
          $amostra = new AmostrasModel();
          $amostra->idcontrato = $this->request->request->get('idcontrato');
          $amostra->numetiqueta = $this->request->request->get('idetiqueta');
          $amostra->dtamostra = $this->request->request->get('dtamostra');
          $amostra->idtipoAmostra = $this->request->request->get('idtpamostra');
          $amostra->protocolo = $this->request->request->get('protocolo');
          $amostra->observacao = $this->request->request->get('observacao');
          $amostra->idoperador = $rs_operador[0]['id'];
          $amostra->idoperadorentrega = $rs_operEntrega[0]['id'];
          $amostra->ativo = 'S';
          $amostra->finalizado = 'N';
          $amostra->create();

        }else{

          $amostra = AmostrasModel::find($id);
          $amostra->idcontrato = $this->request->request->get('idcontrato');
          $amostra->numetiqueta = $this->request->request->get('idetiqueta');
          $amostra->dtamostra = $this->request->request->get('dtamostra');
          $amostra->idtipoAmostra = $this->request->request->get('idtpamostra');
          $amostra->protocolo = $this->request->request->get('protocolo');
          $amostra->observacao = $this->request->request->get('observacao');
          $amostra->idoperador = $rs_operador[0]['id'];
          $amostra->idoperadorentrega = $rs_operEntrega[0]['id'];
          $amostra->update();

        }
        Transaction::close();
        echo json_encode(['retorno' => 200, 'data' => $amostra]);
    }

    public function cnpj()
    {
      Transaction::open('valorem');
      $inpcnpj = $this->input->post('numcliente');
      $cnpj = ClientesModel::all("numcliente = '{$inpcnpj}'");
      if(count($cnpj)){
        $this->jsonResponse([ 'status' => '400', 'data' => false ]);
      }
      $this->jsonResponse([ 'status' => '400', 'data' => false ]);
      Transaction::close();
    }

    public function edit()
    {
        $idamostra = $this->request->request->get('idamostra');
        Transaction::open('valorem');

        $amostra = new AmostrasModel();
        $amostra->where('lab_amostra.id','=',$idamostra);
        $rs_amostra = $amostra->get();


        $contratoModel = ContratosModel::find($rs_amostra[0]['idcontrato']);
        $clientesModel = ClientesModel::find($contratoModel->idcliente);
        $operadorModel = OperadorModel::find($rs_amostra[0]['idoperador']);
        $operEntregaModel = OperadorModel::find($rs_amostra[0]['idoperadorentrega']);

        $taModel = TipoAmostraModel::All('ativo="S"');

        $arrCliente = ['id' => $clientesModel->id, 'nome' => $clientesModel->nmcliente];
        $arrContrato = ['id' => $contratoModel->id, 'hibrido' => $contratoModel->hibrido, 'num' => $contratoModel->numcontrato];
        $arrOperador = ['id' => $operadorModel->cod, 'name' => $operadorModel->name];
        $arrOperEntrega = ['id' => $operEntregaModel->cod, 'name' => $operEntregaModel->name];


        $this->userData('amostra',$rs_amostra[0]);
        $this->userData('cliente', $arrCliente);
        $this->userData('contrato', $arrContrato);
        $this->userData('operador', $arrOperador);
        $this->userData('idetiqueta', $rs_amostra[0]['numetiqueta']);
        $this->userData('tipoAmostras', $taModel);

        $this->render('amostras_loadfields');

        Transaction::close();
    }

    public function delete(){
       try{
           Transaction::open('valorem');

          $idamostra = $this->input->delete('id');

          $teste = new TesteModel();
          $teste->fillable('id');
          $teste->where('idamostra','=',$idamostra);
          $rs_teste = $teste->get();

           $contarTeste = count($rs_teste);

           if($contarTeste > 0){
               $json = ['status' => 400,'message' => 'Já possui teste cadastrado para essa amostra'];
               $this->jsonResponse($json);
           }else{
               $amostra = AmostrasModel::find($idamostra);
               $amostra->ativo = 'N';
               $amostra->update();

               $json = ['status' => 200,'message' => 'Amostra excluida com sucesso'];
               $this->jsonResponse($json);
        }
           Transaction::close();
       }catch(\Exception $e){
           Transaction::rollback();
           $json = [ 'status' => 500,'message' => $e->getMessage()];
           $this->jsonResponse($json);
       }
    }
}
