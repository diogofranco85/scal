<?php

namespace Core\Html;


class Table extends Element{

    public function __construct()
    {
        parent::__construct('table');
    }


    public function addRow(){
        $row = new TableRow();
        parent::add($row);
        return $row;
    }

    public function addHeader($itens){

        $head = new Element('thead');

        $html = '';
        foreach($itens as $item){
            $row = $this->addRow();
            $row->addCell($item);

            $html .= $row->show()."\n";
        }

        $head->add($html);
        $head->show();

    }



}