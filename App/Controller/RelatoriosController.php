<?php

namespace App\Controller; 

use \App\Src\MY_Controller;
use App\Model\ClientesModel;
use App\Model\ContratosModel;
use App\Model\EtiquetasModel;
use App\Model\AmostrasModel;
use App\Model\TesteModel;
use App\Model\EstoqueModel;

use \Core\Database\Transaction;

class RelatoriosController extends MY_Controller {

    public function __construct(){ 
        parent::__construct();
        $this->authenticate();
          
      }

    public function index(){
        
        $this->render('relatorios/index');
    }


    public function rastreabilidade_index(){

    }

    public function rastreabilidade($idetiqueta){
        Transaction::open('valorem');

        $columns = [
            'lab_etiquetas' => ['id as idetiqueta','created_at as dtetiqueta'],
            'lab_cliente' => ['nmcliente'],
            'lab_contrato' => ['hibrido','numcontrato','finalizado'],
            'lab_safra' => ['descricao as safra'],
            'lab_operador' => ['name'],
        ];

        $etiquetas = new EtiquetasModel();
        $etiquetas->fillable($columns);
        $etiquetas->innerJoin('lab_cliente','id','idcliente');
        $etiquetas->innerJoin('lab_contrato','id','idcontrato');
        $etiquetas->innerJoin('lab_safra','id','idsafra','lab_contrato');
        $etiquetas->innerJoin('lab_operador','id','idoperador');
        $etiquetas->where('lab_etiquetas.id','=',$idetiqueta);
        
        $columns_amostra = [
            'lab_amostra' => ['id as idamostra','protocolo','observacao','dtamostra'],
            'lab_tipoamostra' => ['descricao as tipo','setor'],
            'lab_operador' => ['name']
        ];

        $amostra = new AmostrasModel();
        $amostra->fillable($columns_amostra);
        $amostra->innerJoin('lab_etiquetas','id','numetiqueta','lab_amostra');
        $amostra->innerJoin('lab_tipoamostra','id','idtipoamostra');
        $amostra->innerJoin('lab_operador','id','idoperador');
        $amostra->where('lab_amostra.numetiqueta','=', $idetiqueta);
        

        $columns_teste = [
            'lab_teste' => ['dtinicio', 'dttermino'],
            'operinicio' => ['name as "nameinicio"'],
            'operfim' => ['name as "namefim"'],
        ];

        $testes = new TesteModel();
        $testes->fillable($columns_teste);
        $testes->innerJoin( ['table' => 'lab_operador', 'as' => 'operinicio'],'id','idoperador_inicio','lab_teste');
        $testes->innerJoin(['table' => 'lab_operador', 'as' => 'operfim'],'id','idoperador_termino','lab_teste'); 
        $testes->where('idetiqueta','=',$idetiqueta);

        $estoque = new EstoqueModel();
        $estoque->fillable([ 'lab_estoque' => ['*'], 'lab_operador' => ['name']]);
        $estoque->innerJoin('lab_operador','id','idoperador');
        $estoque->where('idetiqueta','=',$idetiqueta);


        $rs_estoque = $estoque->get();
        $rs_testes =$testes->get();
        $rs_amostra = $amostra->get();
        $rs_etiqueta = $etiquetas->get();

        $this->userData('estoques',$rs_estoque);
        $this->userData('testes',$rs_testes);
        $this->userData('amostras', $rs_amostra);
        $this->userData('etiquetas',$rs_etiqueta);

        Transaction::close();

        $this->render('relatorios/rastreabilidade_rel');
    }

}