<?php

namespace App\Controller;

use Core\Controller;
use Core\Database\Transaction;
use App\Model\UsuariosModel;
use App\Model\ModulosModel;
use App\Model\PermissaoModel;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SimController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function index(){
        if($this->session->logged == true){
            $this->redirect('/scal');
        }else{
            $this->redirect('sim',true);
        }
    }

    public function autenticar(){

        Try{
            Transaction::open("usuario");

            $user = $this->input->post('usuario');
            $pass = $this->input->post('senha');
            $module = $this->input->post('modulo');

            $modulo = new ModulosModel();
            $usuario = new UsuariosModel();
            $permissao = new PermissaoModel();

            if($usuario->validarUsuario($user)){
                if($usuario->usuarioAtivo($user)){
                    if($usuario->validarSenha($user, $pass)){
                        if($modulo->verificaAtivado($module)){
                            if($usuario->verificarLogin($user, $pass, $module)){
                                $this->session->usuario = $usuario->getData();
                                $this->session->logged = true;
                                echo 1;
                            }else{
                                echo 14;
                            }
                        }else{
                            echo 13;
                        }
                    }else{
                        echo 12;
                    }
                }else{
                    echo 11;
                }
            }else{
                echo 10;
            }

        }catch(\Exception $e){
            $this->jsonResponse([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function sim(){
        $this->redirect('http://172.16.20.9/scal/dashboard',true);
    }

    public function logout(){

        $this->session->logged = false;
        $this->redirect('sim', true);


    }

    public function verificarLogin(){
            $this->jsonResponse(['logged' => $this->session->logged]);
    }

}