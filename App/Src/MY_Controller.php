<?php

namespace App\Src;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MY_Controller extends Controller{

    public function __construct()
    {
        parent::__construct();
    }


    protected function authenticate()
    {  
        if($this->session->logged)
        {
            $this->userdata('usuario', $this->session->usuario);
        }else{
            $redirect = new RedirectResponse('/sim');
            $redirect->send();
        }

        $this->userData('btnnew','btn btn-sm btn-success btn-flat');
        $this->userData('btnsave','btn btn-sm btn-primary btn-flat');
        $this->userData('btnexc','btn btn-sm btn-danger btn-flat');
        $this->userData('btnedit','btn btn-sm btn-info btn-flat');
        $this->userData('btnprint','btn btn-sm btn-flat bg-purple');
        $this->userData('btnview','btn btn-sm btn-flat bg-purple');

        $this->userData('iconnew','fa fa-plus fa-fw');
        $this->userData('iconsave','fa fa-save fa-fw');
        $this->userData('iconexc','fa fa-trash fa-fw text-danger');
        $this->userData('iconedit','fa fa-pencil fa-fw');
        $this->userData('iconclose','fa fa-sign-out fa-fw');
        $this->userData('iconprint','fa fa-print fa-fw');
        $this->userData('iconview','fa fa-eye fa-fw');

        $this->userData('datatable_class','table table-bordered table-responsive dataTable table-condensed');

        $this->userData('limit_data','50');


    }

}