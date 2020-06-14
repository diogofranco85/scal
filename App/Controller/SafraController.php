<?php

namespace App\Controller;

use App\Src\MY_Controller;
use Core\Database\Transaction;
use App\Model\SafraModel;

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
        $safra->select();
        $rs = $safra->get();

        Transaction::close();

        $this->userData('datas',$rs);
        $this->userData('page_title','Safras');
        $this->render('safra');
    }

    public function store(){
        Transaction::open('valorem');
        $descricao = $this->request->request->get('descricao');

        $safra = new SafraModel();
        $safra->descricao = $descricao;
        $safra->created_at = date('Y-m-d h:m:s');
        $safra->create();

        Transaction::close();

        $this->jsonResponse(['code' => 200, 'message' => 'Safra adicionada com sucesso']);
    }

    public function update(){
        
        try{
            Transaction::open('valorem');
            $id = $this->request->request->get('id');
            $descricao = $this->request->request->get('descricao');

            $safra = SafraModel::find($id);
            $safra->descricao = $descricao;
            $safra->updated_at = date('Y-m-d h:m:s');
            $safra->update();

            Transaction::close();

            $this->jsonResponse(['code' => 200, 'message' => 'Safra adicionada com sucesso']);
        }catch(Exception $e){
            Transaction::rollback();
            $this->jsonResponse([
                'code' => 401, 
                'message' => 'Error ao atualizar informação', 
                'erro' => $e->getMessage
                ]);
        }
        
    }

}