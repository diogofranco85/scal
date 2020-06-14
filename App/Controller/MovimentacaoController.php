<?php

namespace App\Controller;

use \Core\Database\Transaction;
use App\Model\ArmazenamentoModel;
use App\Model\DivisaoModel;
use App\Model\EstoqueModel;
use App\Model\PrateleirasModel;
use App\Model\EtiquetasModel;
use App\Model\StMovimentacaoModel;
use \App\Src\MY_Controller;

class MovimentacaoController extends \App\Src\MY_Controller
{
    public function __construct(){

      parent::__construct();
      $this->authenticate();

    }

    public function index($idarm, $idetiqueta){

      Transaction::open('valorem');

       // $prateleiras = PrateleirasModel::All();
        //$divisoes = DivisaoModel::All();

        $coluna = [
            'lab_estoque' => ['id', 'armazenamento','idetiqueta','id_status_end'],
            'void' => ['sum(lab_estoque.peso) as "calcpeso"'],
            'lab_cliente' => ['nmcliente'],
            'lab_contrato' => ['numcontrato','hibrido'],
        ];

        $estoque = new EstoqueModel();
        $estoque->fillable($coluna);
        $estoque->innerJoin('lab_etiquetas','id','idetiqueta');
        $estoque->innerjoin('lab_cliente','id', 'idcliente', 'lab_etiquetas');
        $estoque->innerJoin('lab_contrato','id', 'idcontrato','lab_etiquetas');
        $estoque->group('lab_estoque.idetiqueta');
        //$estoque->where('idarmazenamento','=',$idarm);
        $estoque->where('idetiqueta','=',$idetiqueta);
        $rs_estoque = $estoque->get();

        //prateleiras
        
        //status
        $rs_status = StMovimentacaoModel::All();

        $this->userData('rs_estoque', $rs_estoque);
        $this->userData('rs_status', $rs_status);
        $this->userData('page_title','Movimentação de amostras');
        Transaction::close();

        $this->render('mov_index');

    }

    public function move(){
      Transaction::open('valorem');
      try{
        $estoque = new EstoqueModel(); 
        $estoque->armazenamento = $this->input->post('estoqueatual');
        $estoque->idetiqueta = $this->input->post('idetiqueta');
        $estoque->peso = ($this->input->post('peso')*(-1));
        //$estoque->pesoinicial = $this->input->post('pesoatual');
        $estoque->id_status_mov = $this->input->post('status_mov');
        $estoque->id_status_end = $this->input->post('status_end');
        $estoque->updated_at = date('Y-m-d H:i:s');
        $estoque->tipo = 'BAIXA';
        $estoque->create();

        $estoque2 = new EstoqueModel(); 
        $estoque2->armazenamento = $this->input->post('local');
        $estoque2->idetiqueta = $this->input->post('idetiqueta');
        $estoque2->peso = $this->input->post('peso');
        //$estoque2->pesoinicial = $this->input->post('pesoatual');
        $estoque2->id_status_mov = $this->input->post('status_mov');
        $estoque2->id_status_end = $this->input->post('status_end');
        $estoque2->tipo = 'MOVIMENTACAÇÃO';
        $estoque2->updated_at = date('Y-m-d H:i:s');
        $estoque2->create();

        $this->jsonResponse([ 'status' => 200 , 'message' => 'Movimentação realizada com sucesso']);

      }catch(\Exception $e){
        $this->jsonResponse([ 'status' => 500 , 'message' => $e->getMessage()]);
      }
      
      Transaction::close();
    }
}