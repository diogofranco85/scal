<?php

  namespace App\Controller;
  
  use App\Model\TipoAmostraModel;
  use App\Model\AmostrasModel;
  use \App\Src\MY_Controller;
  use \Core\Database\Transaction;

  class TipoAmostrasController extends MY_Controller{

    public function __construct(){ 
      parent::__construct();
      $this->authenticate();
        
    }

    public function index()
    {
      Transaction::open('valorem');
      $tp = TipoAmostraModel::all('ativo = "S"');
      Transaction::close();

      $this->userData('grids',$tp);
      $this->userData('page_title','Tipos de Amostras');
      $this->render('tpamostras_index');
    }

    public function edit($id)
    {
        try{
          Transaction::open('valorem');
          $tipoAmostra = TipoAmostraModel::find($id, 'id, descricao, setor');
          $ddTipoAmostra = $tipoAmostra->toArray();
          
          $this->jsonResponse(['status' => 200, 'result' => $ddTipoAmostra]);

          Transaction::close();
        }catch(\Exception $e){
          $this->jsonResponse(['status' => 500, 'message' => $e->getMessage], 500);

        }
    }

    public function save()
    {
          try{
            Transaction::open('valorem');
          
            $id = $this->input->body('id');
            
            $usuario = $this->session->usuario;

            if($id == ''){
                $tp = new TipoAmostraModel();
                $tp->descricao = \strtoupper($this->input->body('descricao'));
                $tp->setor = $this->input->body('setor');
                $tp->created_at = date('Y-m-d h:m:s');
                $tp->created_id = $usuario['idUsuario'];
                $tp->create();

                $this->jsonResponse(array('status' => 200, 'message' => 'Tipo de amostra <strong>adicionado</strong> com sucesso'));
            }else{
                $tp = TipoAmostraModel::find($id);
                $tp->descricao =  \strtoupper($this->input->body('descricao'));
                $tp->setor = $this->input->body('setor');
                $tp->updated_at = date('Y-m-d h:m:s');
                $tp->updated_id = $usuario['idUsuario'];
                $tp->update();

                $this->jsonResponse(array('status' => 200, 'message' => 'Tipo de amostra <strong>atualizado</strong> com sucesso'));
            }
            Transaction::close();
          }catch(Exception $e){
            $this->jsonResponse(array('status' => 500, 'message' => $e->getMessage));
          }
          
    }

    public function delete(){
      try{
        Transaction::open('valorem');

        $id = $this->input->body('id');
        $usuario = $this->session->usuario;

        $amostras = new AmostrasModel();
        $amostras->where('idtipoamostra','=',$id);
        $amostras->where('ativo','=','S');
        
        if(count($amostras->get()) > 0){
          $this->jsonResponse(array('status' => 400, 'message' => 'Tipo já vinculada a amostra'));
          return;
        };

        $tp = TipoAmostraModel::find($id);
        $tp->ativo = 'N';
        $tp->updated_at = date('Y-m-d h:m:s');
        $tp->updated_id = $usuario['idUsuario'];
        $tp->update();

        $this->jsonResponse(array('status' => 200, 'message' => 'Tipo de amostra <strong>excluido</strong> com sucesso'));

        Transaction::close();
      }catch(\Exception $e){
          $this->jsonResponse(array('status' => 500, 'message' => "{$e->getMessage()}<br><br><code>{$e->getTraceAsString()}</code>"));
      }
    }


  }