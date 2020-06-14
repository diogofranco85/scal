<?php

namespace App\Controller;


use App\Model\AmostrasModel;
use App\Model\ClientesModel;
use App\Model\EtiquetasModel;
use App\Model\TesteModel;
use App\Model\OperadorModel;
use App\Src\MY_Controller;
use Core\Database\Transaction;

class AmostraTesteController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authenticate();
    }

    public function index(){
        Transaction::open('valorem');

        $coluna = array(
            'lab_teste' => [ 'id','idamostra','dtinicio','dttermino'],
            'lab_amostra' => ['idcontrato'],
            'lab_contrato' => ['hibrido','numcontrato'],
            'lab_cliente' => ['nmcliente'],
            'lab_tipoamostra' => ['descricao as "tipo"'],
            'operinicio' => ['name as inicioname'],
            'operfim' => ['name as terminoname'],
            'lab_etiquetas' => ['setor'],
            'lab_etiquetas' => ['id as "numetiqueta"']
        );

        $teste = new TesteModel();
        $teste->fillable($coluna);
        $teste->innerJoin('lab_amostra','id','idamostra');
        $teste->innerJoin('lab_etiquetas','id','idetiqueta');
        $teste->innerJoin('lab_tipoamostra','id','idtipoamostra','lab_amostra');
        $teste->innerJoin('lab_contrato','id','idcontrato', 'lab_amostra');
        $teste->innerJoin('lab_cliente','id','idcliente','lab_contrato');
        $teste->innerJoin( ['table' => 'lab_operador', 'as' => 'operinicio'],'id','idoperador_inicio','lab_teste');
        $teste->leftJoin(['table' => 'lab_operador', 'as' => 'operfim'],'id','idoperador_termino','lab_teste');
        $teste->where('lab_teste.ativo','=','S');
        $teste->where('lab_contrato.finalizado','=','N');
        $rs = $teste->get();

        Transaction::close();

        $this->userData('testes',$rs);
        $this->userData('page_title','Teste de Amostras');
        $this->render('teste_index');

    }

    public function loadfields(){

        Transaction::open('valorem');
        $id = $this->request->request->get('idetiqueta');
        $amostra = new AmostrasModel();
        $amostra->fillable('count(*) as c');
        $amostra->where('numetiqueta','=',$id);
        $rs_amostra = $amostra->get();
        
        
        if($rs_amostra[0]['c'] == 1 ){

             $colunas = [
                 'lab_cliente' => ['nmcliente'],
                 'lab_contrato' => ['id as idcontrato','numcontrato','hibrido'],
                 'lab_etiquetas' => ['setor'],
                 'lab_amostra' => ['id as idamostra'],
             ];

             $amostra = new AmostrasModel();
             $amostra->fillable($colunas);
             $amostra->innerJoin('lab_etiquetas','id','numetiqueta');
             $amostra->innerJoin('lab_cliente','id', 'idcliente','lab_etiquetas');
             $amostra->innerJoin('lab_contrato','id', 'idcontrato','lab_etiquetas');
             $amostra->where('lab_amostra.numetiqueta','=',$id);
             $rs = $amostra->get();
             
             $data['dtinicio'] = date('d-m-Y');
             $data['hrinicio'] = date('h:i:s');
            
            $this->jsonResponse(['code' => 200, 'data' => array_merge($rs[0], $data) ]);
            

        }else{
            $this->jsonResponse(['code' => 400, 'message' => 'Não possui amostra cadastrada']);
        }



        Transaction::close();

    }

    public function loadview(){

        Transaction::open('valorem');

        $idteste = $this->request->request->get('idteste');

        $colunas = [
            'lab_amostra' => ['id as "idamostra"'],
            'lab_cliente' => ['id as "idcliente"','nmcliente'],
            'lab_contrato' => ['id as "idcontrato"','numcontrato','hibrido'],
            'lab_teste' => ['dtinicio', 'dttermino'],
            'lab_etiquetas' => ['id as "numetiqueta"', 'setor'],
            'operinicio' => ['name as "nameinicio"'],
            'operfim' => ['name as "namefim"'],
        ];

        $cliente = new AmostrasModel();
        $cliente->fillable($colunas);
        $cliente->innerJoin('lab_contrato','id','idcontrato');
        $cliente->innerJoin('lab_cliente','id','idcliente','lab_contrato');
        $cliente->innerJoin('lab_teste','idamostra','id');
        $cliente->innerJoin('lab_etiquetas','idamostra','id');
        $cliente->innerJoin( ['table' => 'lab_operador', 'as' => 'operinicio'],'id','idoperador_inicio','lab_teste');
        $cliente->innerJoin(['table' => 'lab_operador', 'as' => 'operfim'],'id','idoperador_termino','lab_teste');    
        $cliente->where('lab_teste.id','=',$idteste);
        $rs_cliente = $cliente->get();

        $this->userData('amostra',$rs_cliente);

        $this->render('teste_loadfields_views');


        Transaction::close();

    }

    public function save(){
        

        try{

            Transaction::open('valorem');

            $idamostra = $this->request->request->get('idamostra');
            $idetiqueta = $this->request->request->get('numetiqueta');

            $operador = new OperadorModel();
            $operador->where('cod','=', $this->request->request->get('idoperador'));
            $op = $operador->get();
            $op = $op[0];

            $teste = TesteModel::all("idetiqueta = {$idetiqueta}");
            $contarTeste = count($teste);
        
            if($contarTeste > 0){

                if($teste[0]['dttermino'] == '1001-01-01 01:01:01'){
                    $rs_teste = TesteModel::find($teste[0]['id']);
                    $rs_teste->dttermino = date('Y-m-d H:i:s');
                    $rs_teste->idoperador_termino = $op['id']; 
                    $rs_teste->update();
                }else{
                    $json = [ 'code' => '200', 'message' => 'Teste finalizado'];
                    $this->jsonResponse($json);
                }

                //$json = [ 'code' => '200', 'message' => 'Teste finalizado'];
            }else{
                $rs_teste = new TesteModel();
                $rs_teste->dtinicio = date('Y-m-d H:i:s');
                $rs_teste->idamostra = $idamostra;
                $rs_teste->idetiqueta = $idetiqueta;
                $rs_teste->dttermino = '1001-01-01 01:01:01';
                $rs_teste->idoperador_inicio =  $op['id']; 
                $rs_teste->create();
                $json = [ 'code' => '200', 'message' => 'Teste iniciado'];
                $this->jsonResponse($json);
            }

            Transaction::close();

        }catch(\Exception $e){
            $json = [ 'code' => '400', 'message' => $e->getMessage()];
            echo json_encode($json);
        }

          //echo json_encode($json);
    }


}