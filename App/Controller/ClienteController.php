<?php

namespace App\Controller;

use App\Model\ContratosModel;
use \App\Src\MY_Controller;
use \App\Model\ClientesModel;
use \Core\Database\Transaction;

class ClienteController extends MY_Controller
{
    public function __construct(){
      parent::__construct();
      $this->authenticate();
    }

    public function index(){

      Transaction::open('valorem');

      $clientes = ClientesModel::all('ativo = "S"');
      $this->userData('clientes',$clientes);
      $this->render('cliente_index');
      Transaction::close();

    }

    public function edit()
    {

      Transaction::open('valorem');
      $cliente = ClientesModel::find($this->input->post('id'),'id,nmcliente,numcliente, descricao ,codprotheus');
      $ddCliente = $cliente->toArray();
      echo json_encode($ddCliente);
      Transaction::close();

    }

    public function save()
    {
        Transaction::open('valorem');
        $id = $this->request->request->get('id');
        $usuario = $this->session->usuario;
        if($id == ''){

          $clientes = new ClientesModel();
          $clientes->nmcliente = \strtoupper($this->request->request->get('nmcliente'));
          $clientes->descricao = \strtoupper($this->request->request->get('descricao'));
          $clientes->created_at = date('Y-m-d h:m:s');
          $clientes->created_id = $usuario['idUsuario'];
          $clientes->create();

        }else{

          $clientes = ClientesModel::find($id);
          $clientes->nmcliente =  \strtoupper($this->request->request->get('nmcliente'));
          $clientes->descricao = \strtoupper($this->request->request->get('descricao'));
          $clientes->updated_at = date('Y-m-d h:m:s');
          $clientes->updated_id = $usuario['idUsuario'];
          $clientes->update();
          
        }
        Transaction::close();

        echo json_encode(array('retorno' => 200));
    }

    public function delete()
    {
        try{
            Transaction::open('valorem');
            $id = $this->request->request->get('idcliente');

            //verificar se existe contrato para esse cliente
            // se existe retorna codigo 400 senao retorna codigo 200

            $contrato = ContratosModel::all("idcliente = {$id} AND ativo='S'");
            $contarContratosAtivos = count($contrato);

            // retorna o nome do cliente para exibir na mensagem de retorno
            $cliente = ClientesModel::find($id);

            if($contarContratosAtivos > 0)
            {
                $json = [ 'code ' => '400','message' => "{$cliente->nmcliente}<br> possui contratos ativos no sistema"];
            }else{
                $cliente = ClientesModel::find($id);
                $cliente->ativo = 'N';
                $cliente->update();
                Transaction::close();
                $json = array('code' => '200', 'message' => $cliente->nmcliente . '<br>Excluindo cliente ... <i class="fa fa-spinner fa-spin"></i>');
            }


        }catch (\Exception $e){
            Transaction::rollback();
            $json = array('code' => '500', 'message' => 'Houve um erro no sistema <br>' . $e->getMessage());
        }

        echo json_encode($json);

    }

    public function cnpj()
    {

      //consultar se o cnpj ja existe na base de dados

      Transaction::open('valorem');
      $inpcnpj = $this->input->post('numcliente');
      $cnpj = ClientesModel::all("numcliente = '{$inpcnpj}'");
      if(count($cnpj) > 0){
        $this->jsonResponse([ 'status' => '200', 'data' => true ]);
      }else{
        $this->jsonResponse([ 'status' => '400', 'data' => false ]);
      }

      Transaction::close();
    }

    public function csv(){
      try{
        Transaction::open('valorem');

        $file = ROOT.DS.'Public'.DS.'SA1010_201909171405.csv';
        $delimiter = ',';
        $handler = fopen($file, 'r');
        $cabecalho = fgetcsv($handler, 0, $delimiter);

        //__d($cabecalho);
        $cont = 1;
        while(!feof($handler)){
          $line = \fgetcsv($handler, 0, $delimiter); 
          if(!$line){
            continue;
          }
          $registro = array_combine($cabecalho, $line);

          if ($registro['A1_PESSOA'] == 'J'){
            $cnpj = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", rtrim($registro['A1_CGC']));

            $cliente = new ClientesModel();
            $cliente->nmcliente = rtrim($registro['A1_NOME']);
            $cliente->descricao = rtrim($registro['A1_NREDUZ']);
            $cliente->endereco = rtrim($registro['A1_END']);
            $cliente->estado = rtrim($registro['A1_EST']);
            $cliente->municipio = rtrim($registro['A1_MUN']);
            $cliente->cep = rtrim($registro['A1_CEP']);
            $cliente->numcliente = $cnpj;
            $cliente->codprotheus = rtrim($registro['R_E_C_N_O_']);
            $cliente->ativo = 'S';
            $cliente->created_id = 1;
            $cliente->created_at = date('Y-m-d h:i:s');
            $cliente->updated_id = 1;
            $cliente->updated_at = date('Y-m-d h:i:s');
           
            $cliente->create(); 
            
            $cont++;
          }
                  
        }

        fclose($handler);
        
        Transaction::close();
        $this->jsonResponse([
          'status' => '200', 
          'message' => 'Dados salvos com sucesso',
          'registros' => $cont
          ]);
      }catch(\Exception $e){
        echo $e->getMessage();
        Transaction::rollback();
      }
    }
}
