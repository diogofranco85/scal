<?php


namespace App\Model;


use Core\Model;

class ModulosModel extends Model{


    public function __construct($schema = 'modulos', $key = 'idModulos'){
        parent::__construct($schema, $key);
    }

    public function verificaAtivado($sigla){
        try{
            $this->where('sigla', '=', $sigla);
            $this->where('status', '=',1);
            $rs = $this->get();

            if(count($rs) > 0){
                return true;
            }

            return false;

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

}