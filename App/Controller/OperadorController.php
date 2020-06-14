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
        $codigo = $this->request->request->get('id');
        $operador = OperadorModel::all("cod = {$codigo}");
        
        if(count($operador) > 0){
            echo json_encode( ['code' => 200, 'data' => $operador[0] ]);
        }else{
            echo json_encode( [ 'code' => '404','message' => 'Operator not found']);
        }

        Transaction::close();
        
    }

    public function index($id){
        Transaction::open('valorem');

        $operador = new OperadorModel();
        $operador->where('cod','=', $id);
        
        $rs = $operador->get();

        $this->jsonResponse($rs[0]);

        Transaction::close();
    }



}