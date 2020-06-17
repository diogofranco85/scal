<?php

namespace App\Controller;

use App\Src\MY_Controller;
use Core\Database\Transaction;
use App\Model\SafraModel;
use App\Model\ContratosModel;

class SafraController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authenticate();
    }

    public function index(){

        Transaction::open('valorem');
        
        $safra = new SafraModel();
        $safra->where('ativo','=','S');
        $rs = $safra->get();

        Transaction::close();

        $this->userData('datas',$rs);
        $this->userData('page_title','Safras');
        $this->render('safra');
    }

    public function store(){
        try{
            Transaction::open('valorem');
            $descricao = $this->input->body('descricao');
            $id = $this->input->body('id');
            
            if($id == false){
                $safra = new SafraModel();
                $safra->descricao = $descricao;
                $safra->created_at = date('Y-m-d h:m:s');
                $safra->create();

                $this->jsonResponse(['status' => 200, 'message' => 'Safra adicionada com sucesso']);
            }else{
                $safra = SafraModel::find($id);
                $safra->descricao = $descricao;
                $safra->updated_at = date('Y-m-d h:m:s');
                $safra->update();

                $this->jsonResponse(['status' => 200, 'message' => 'Safra atualizada com sucesso']);
            }

            Transaction::close();

            
        }catch(Exception $e){
            $this->jsonResponse(['status' => 500, 'message' => "Erro ao adicionar registro <br> {$e->getMessage()}"]);
        }
    }

    public function update($id){
        
        try{
            Transaction::open('valorem');

            $safra = new SafraModel();
            $safra->fillable('id,descricao, finalizado');
            $safra->where('id','=',$id);

            Transaction::close();

            $this->jsonResponse(['status' => 200, 'result' => $safra->get()[0]]);
        }catch(Exception $e){
            Transaction::rollback();
            $this->jsonResponse([
                'status' => 401, 
                'erro' => $e->getMessage
                ]);
        }
        
    }

    public function delete(){
        try{
            Transaction::open('valorem');
                $id = $this->input->delete('id');

                $contrato = new ContratosModel();
                $contrato->where('idsafra','=',$id);

                if(count($contrato->get()) > 0){
                    $this->jsonResponse(['status' => 400, 'message' => 'Existe contrato para essa safra cadastrado <br> É necessário excluir todos os contratos antes de excluir essa safra']);
                    return;
                }


                $safra = SafraModel::find($id);
                $safra->ativo = 'N';
                $safra->update();

                $this->jsonResponse(['status' => 200, 'message' => 'Safra excluida com sucesso']);

            Transaction::close();
        }catch(Exception $e){
            $this->jsonResponse(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

}