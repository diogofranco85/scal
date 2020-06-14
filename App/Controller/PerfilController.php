<?php

namespace App\Controller;

use App\Src\MY_Controller;
use Core\Database\Transaction;
use App\Model\PermissaoModel;

class PerfilController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authenticate();
    }

    public function index(){
        $idModulo = 15;

        Transaction::open('usuario');

        $colunas = [
            'usuario' => ['idUsuario as id', 'usuario','nome','email'],
            'permissao' => ['tipo'],

        ];

        $permissao = new PermissaoModel();
        
        $permissao->fillable($colunas);
        $permissao->innerJoin('usuario','idUsuario', 'idUsuario');
        $permissao->innerJoin('modulos','idModulos','idModulo');
        $permissao->where('permissao.idModulo', '=', $idModulo);
        $permissao->where('permissao.tipo' ,'>', '0');
        $permissao->order('usuario.nome ASC');
        $rs = $permissao->get();

        Transaction::close();

        $this->userData('grids', $rs);
        $this->userData('page_title','Perfil de acesso');

        $this->render('perfil');
    } 

}