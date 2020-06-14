<?php
/**
 * Created by PhpStorm.
 * User: diogo
 * Date: 11/10/19
 * Time: 12:05
 */

namespace App\Controller;

use App\Model\ArmazenamentoModel;
use App\Model\DivisaoModel;
use App\Model\EstoqueModel;
use App\Model\PrateleirasModel;
use App\Model\EtiquetasModel;
use App\Model\OperadorModel;
use \App\Src\MY_Controller;
use Core\Database\Transaction;

class EnderecamentoController extends MY_Controller
{

    public function __construct(){
        parent::__construct();
        $this->authenticate();
    }

    public function index(){

        Transaction::open('valorem');

        $coluna= [
            'lab_etiquetas' => ["id as idetiqueta",'setor'],
            'lab_estoque' => ['id', 'armazenamento','peso_armazem', 'peso_cliente', 'peso_descarte', 'tipo'],
            'lab_contrato' => ['numcontrato','hibrido'],
            'lab_cliente' => ['nmcliente'],
        ];

        $estoque = new EstoqueModel();
        $estoque->fillable($coluna);
        $estoque->innerJoin('lab_etiquetas','id','idetiqueta');
        $estoque->innerjoin('lab_cliente','id', 'idcliente', 'lab_etiquetas');
        $estoque->innerJoin('lab_contrato','id', 'idcontrato','lab_etiquetas');
        $estoque->where('lab_contrato.finalizado','=','N');
        $rs_estoque = $estoque->get();

        $this->userData('rs_estoque', $rs_estoque);
        $this->userData('page_title','Endereçamento de amostras');
        Transaction::close();

        $this->render('enderecamento_index');
    }

    public function loadfields(){

        $this->render('enderecamento_loadfields');

    }

    public function prateleiras(){

        Transaction::open('valorem');

        $idprateleira = $this->request->request->get('idarmazenamento');

        $armazenamento = new ArmazenamentoModel();
        $rs_armazenamento = $armazenamento->storageVoid();

        $html = '';

        foreach($rs_armazenamento as $rsa){
            $id = $rsa['id'];
            $descricao = $rsa['descricao'];
            $selected = ( $idprateleira == $id ) ? 'selected="selected"' : '';

            $html .= "<option value='{$id}' {$selected} >{$descricao}</option> \n";
        }

        echo $html;

        Transaction::close();

    }

    public function status(){
        Transaction::open('valorem');
        $status =  \App\Model\StEnderecamentoModel::All();

        $html = '';

        foreach($status as $rs){
            $id = $rs['id'];
            $descricao = $rs['descricao'];
            $html .= "<option value='{$id}' {$selected} >{$descricao}</option> \n";
        }

        echo $html;

        Transaction::close();
    }

    public function store($idcliente){
        
        Transaction::open('valorem');
        $estoque = new EstoqueModel();

        $operador = new OperadorModel();
        $operador->where('cod','=',$this->request->request->get('idoperador'));
        $rs_operador = $operador->get(); 

        $estoque->idetiqueta = $this->request->request->get('numetiqueta');
        $estoque->armazenamento = $this->request->request->get('armazenamento');
        $estoque->peso_armazem = $this->request->request->get('pesoarmazem');
        $estoque->peso_cliente = $this->request->request->get('pesocliente');
        $estoque->peso_descarte = $this->request->request->get('pesodescarte');
        $estoque->idoperador = $rs_operador[0]['id'];
        //$estoque->id_status_end = $this->request->request->get('status');
        $estoque->tipo = 'ENDERECAMENTO';
        $estoque->created_at = date('Y-m-d h:m:s');
        $estoque->updated_at = date('Y-m-d h:m:s');
        try{
           if($estoque->create()){
                $this->jsonResponse(['status' => 200, 'message' => 'OK']);
            }  
        }catch(Exception $e){
            $this->jsonResponse(['status' => 400, 'message' => $e->getMessage ]);
        }
        

        Transaction::close();
    }

    public function validar(){
        Transaction::open('valorem');
        $numetiqueta = $this->request->request->get('etiqueta');
        $estoque = EstoqueModel::All("idetiqueta = {$numetiqueta}");
        if(count($estoque) > 0){
            $this->jsonResponse(['status' => 401, 'message' => 'Etiqueta ja foi endereçada ']);
        }else{
            $coluna = [
                'lab_etiquetas' => ['id','idcliente','idamostra'],
                'lab_cliente' => ['nmcliente'],
                'lab_contrato' => ['numcontrato','hibrido']
            ];
    
            $etiqueta = new EtiquetasModel();
            $etiqueta->fillable($coluna);
            $etiqueta->innerJoin('lab_cliente','id','idcliente');
            $etiqueta->innerJoin('lab_contrato','id','idcontrato');
            $etiqueta->where('lab_etiquetas.id','=',$numetiqueta);
            $rs_etiqueta = $etiqueta->get();
            if(count($rs_etiqueta) > 0 ){
                $this->jsonResponse(['status' => 200, 'message' => 'OK', 'data' => $rs_etiqueta[0]]);
            }else{
                $this->jsonResponse(['status' => '400', 'message' => 'Etiqueta solicitada não foi localizada no banco']);
            }
        }
        Transaction::close();
    }

    public function teste(){
        Transaction::open('valorem');
        $armazenamento = new ArmazenamentoModel();
        __d($armazenamento->storageVoid());
        Transaction::close();
    } 

    public function  edit($idestoque, $idetiqueta){
         Transaction::open('valorem');

         $estoque_columns = [
                'lab_estoque' => ['id as idestoque', 'armazenamento','peso_armazem','peso_cliente', 'peso_descarte', 'tipo'],
                'lab_etiquetas' => ['id as idetiqueta'],
                'lab_contrato' => ['hibrido','numcontrato'],
                'lab_cliente' => ['nmcliente'],
         ];

         $estoque = new EstoqueModel();
         $estoque->fillable($estoque_columns);
         $estoque->innerJoin('lab_etiquetas','id','idetiqueta');
         $estoque->innerJoin('lab_contrato','id','idcontrato','lab_etiquetas');
         $estoque->innerJoin('lab_cliente','id','idcliente','lab_etiquetas');
         $estoque->where('lab_estoque.id','=', $idestoque);
         
         $rs_estoque = $estoque->get();
         

        //__d($rs_estoque[0]); 

         Transaction::close();
        $this->userData('estoqueAtual', $rs_estoque[0]);
        $this->render('enderecamento_editar');
    }

    public function storeMovimentacao(){

        Transaction::open('valorem');

        try{

        $codOperador = $this->request->request->get('idoperador');

        $operador = new OperadorModel();
        $operador->where('cod','=',$codOperador);
        $rs_operador = $operador->get();


        $estoque = new EstoqueModel();
        $estoque->idetiqueta = $this->request->request->get('idetiqueta');
        $estoque->armazenamento = $this->request->request->get('armazenamento');
        $estoque->peso_armazem = $this->request->request->get('mov_peso_armazem');
        $estoque->peso_cliente = $this->request->request->get('mov_peso_cliente');
        $estoque->peso_descarte = $this->request->request->get('mov_peso_descarte');
        $estoque->idoperador = $rs_operador[0]['id'];
        $estoque->created_at = date('Y-m-d h:i:s');
        $estoque->tipo = 'MOVIMENTAÇÃO';

        $estoque->create();

        $this->jsonResponse(['code' => 200, 'message' => 'Movimentação realizada com sucesso']);

        }catch(Exception $e){
            $this->jsonResponse(['code' => 400, 'message' => $e->getMessage() ]);
        };

        Transaction::close();

    }

}