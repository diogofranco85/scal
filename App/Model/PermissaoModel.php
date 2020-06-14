<?php


namespace App\Model;


use Core\Model;

class PermissaoModel extends Model{

    public function __construct($schema = 'permissao',$key = 'idPermissao'){
        parent::__construct($schema, $key);
    }

    public function access($idUser, $idModule){
        $this->where('idUsuario','=', $idUser);
        $this->where('idModulo','=',$idModule);
        $this->where('tipo','>',0);
        $result = $this->get();

        return $result;
    }

}