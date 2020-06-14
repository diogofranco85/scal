<?php


namespace App\Model;

/**************
 * códigos do sim
 * 11 - Usuário Desativado
 * 12 - Senha incorreta
 * 13 - Módulo desativado
 * 14 - Usuário sem permissão
 * 15 - Usuário sem permissão
 ***************/

use Core\Model;

class UsuariosModel extends Model{

   private $data;

    public function __construct($Schema = 'usuario', $key = 'idUsuario'){
        
        parent::__construct($Schema, $key);
        $this->fillable('idUsuario, usuario, nome, email, funcao, status');
    }

    public function validarUsuario($usuario)
    {
      
        try{

            $this->where('usuario', '=', $usuario);
            $rs = $this->get();
            if(count($rs) > 0){
                return true;
            }

            return false;

        }catch(\Exception $e){
            echo $e->getTraceAsString();
        }

    }

    

    public function validarSenha($usuario, $senha)
    {
        try{ 
            $this->where('usuario','=', $usuario);
            $this->where('senha','=',$senha);
            $rs = $this->get();
            
            $this->data = $rs;

            if(count($rs) > 0){
                return true;
            }

            return false;

        }catch(\Exception $e){
            echo $e->getTraceAsString();
        }

    }

    public function usuarioAtivo($usuario){
        try{
            
            $this->where('usuario','=', $usuario);
            $this->where('status','=',1);
            $rs = $this->get();
            if(count($rs) > 0){
                return true;
            }
                return false;
        
        }catch(\Exception $e){
            echo $e->getTraceAsString();
        }
    }

    public function verificarLogin($user, $pass, $module){
            try{
                $columns = [
                    'usuario' => ['idUsuario', 'usuario', 'email', 'nome', 'funcao'],
                    'permissao' => ['tipo'],
                ];
                $this->fillable($columns);
                $this->innerJoin('permissao','idUsuario','idUsuario');
                $this->innerJoin('modulos','idModulos','idModulo','permissao');
                $this->where('usuario.usuario','=', $user);
                $this->where('usuario.senha','=',$pass);
                $this->where('modulos.sigla','=',$module);
                
                $rs = $this->get();

                $this->data = $rs;
                
                if(count($rs) > 0){
                    return true;
                }
                    return false;
            }catch(\Exception $e){
                echo $e->getTraceAsString();
            }

    }

    public function getData(){
        return $this->data[0];
    }

}