<?php
/**
 * Created by PhpStorm.
 * User: diogo
 * Date: 27/10/19
 * Time: 18:13
 */

namespace App\Controller;


use App\Model\OperadorModel;
use App\Src\MY_Controller;
use Core\Database\Transaction;
use App\Model\AmostraModel;

class OperadorController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authenticate();
    }

    public function getOperador()
    {
        Transaction::open('valorem');
        $codigo = setint($this->input->get('id'));
        $operador = OperadorModel::all("cod = {$codigo}");
        
        if(count($operador) > 0){
            echo json_encode( ['status' => 200, 'data' => $operador[0] ]);
        }else{
            echo json_encode( [ 'status' => '404','message' => 'Operator not found']);
        }

        Transaction::close();
        
    }

    public function index(){
        try{
        
            Transaction::open('valorem');
            $id = $this->input->get('id');

            $operador = new OperadorModel();
            $operador->fillable('id,name');
            $operador->where('cod','=', $id);
            
            $rs = $operador->get();
            
            if(count($rs) == 0){
                $this->jsonResponse(['status' => 400, 'message' => 'Operador não encontrado']);
                return;
            }
            $this->jsonResponse(['status' => 200 , 'result' => $rs[0]]);

            Transaction::close();
        }catch(Exception $e){
            $this->jsonResponse(['status' => 500, 'message' => $e->getMessage()]);
        }

    }



}