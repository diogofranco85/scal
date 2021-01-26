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
      $this->userData('page_title','Clientes');
      $this->render('cliente_index');
      Transaction::close();

    }

    public function edit($id)
    {

     try{
      Transaction::open('valorem');
      
      $cliente = ClientesModel::find($id,'id,nmcliente, descricao');
      $ddCliente = $cliente->toArray();

      $this->jsonResponse(['status' => 200, 'result' => $ddCliente ]);
      
      Transaction::close();

     }catch(Exception $e){

      $this->jsonResponse(['status' => 400, 'message' => $e->getMessage() ]);
     
    }
    }

    public function store()
    {
        try{
          Transaction::open('valorem');
          
          $id = $this->input->body('id');

          $usuario = $this->session->usuario;
          if($id == ''){

            $verificaCliente = new ClientesModel();
            $verificaCliente->where('nmcliente','=', $this->input->body('nmcliente'));
            $verificaCliente->where('ativo','=', 'S');
            $rs_verificaCliente = $verificaCliente->get();

            if(count($rs_verificaCliente) > 0){
              $this->jsonResponse(['status' => '400', 'message' => 'Cliente já existe na banco' ]);
              return;
            }

            $clientes = new ClientesModel();
            $clientes->nmcliente = $this->input->body('nmcliente');
            $clientes->descricao = $this->input->body('descricao');
            $clientes->created_at = date('Y-m-d h:m:s');
            $clientes->created_id = $usuario['idUsuario'];
            $clientes->create();

            $this->jsonResponse(['status' => 200, 'message' => 'Cliente <strong>adicionando</strong> com sucesso' ]);

          }else{

            $clientes = ClientesModel::find($id);
            $clientes->nmcliente = $this->input->body('nmcliente');
            $clientes->descricao = $this->input->body('descricao');
            $clientes->updated_at = date('Y-m-d h:m:s');
            $clientes->updated_id = $usuario['idUsuario'];
            $clientes->update();

            $this->jsonResponse(['status' => 200, 'message' => 'Cliente <strong>Atualizado</strong> com sucesso' ]);
            
          }
          Transaction::close();
          
          

        }catch(Exception $e){
            $this->jsonResponse(['status' => '500', 'message' => $e->getMessage() ], 500);
        }

        
    }

    public function delete()
    {
        try{
            Transaction::open('valorem');
            $id = $this->input->delete('id');

            //verificar se existe contrato para esse cliente
            // se existe retorna codigo 400 senao retorna codigo 200

            $contrato = ContratosModel::all("idcliente = {$id} AND ativo='S'");
            $contarContratosAtivos = count($contrato);

            // retorna o nome do cliente para exibir na mensagem de retorno
            $cliente = ClientesModel::find($id);

            if($contarContratosAtivos > 0)
            {
                $json = [ 'status' => 400,'message' => "O cliente <strong>{$cliente->nmcliente}</strong> <br> possui contratos ativos no sistema"];
            }else{
                $cliente = ClientesModel::find($id);
                $cliente->ativo = 'N';
                $cliente->update();
                Transaction::close();
                $json = array('status' => 200, 'message' => "<strong> $cliente->nmcliente</strong> <br>Cliente excluido com sucesso");
            }


        }catch (\Exception $e){
            Transaction::rollback();
            $json = array('status' => '500', 'message' => 'Houve um erro no sistema <br>' . $e->getMessage());
        }

        $this->jsonResponse($json);

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
