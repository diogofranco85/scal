<?php


namespace Core\Export;

class ExportExcel
{

    private $columns = array();
    private $rows = array();
    private $filename;

    public function __construct($file = 'export_excel.xls'){

        $this->filename = $file;

    }

    public function addColumn($title){

        if(is_array($title)){
            foreach($title as $t){
                $this->columns[] = $t;
            }
        }else{
            $this->columns[] = "$title";
        }

    }

    public function addRow($value = array(), $tag = array()){

        $cTag = '';


        if(count($tag) > 0){

           foreach( $tag as $key => $value ){
               $cTag .= "{$key}='{$value}'";
           }
        }

        $html = "<tr {$cTag}>";

        foreach($value as $v){
            $html .= "<td>{$v}</td> \n";
        }

        $html .= '</tr>';

        $this->rows[] = $html . "\n";
    }






    private function getColumns(){

        $html = "<tr> \n";

        foreach($this->columns as $c){
            $html .= "<th>{$c}</th> \n";
        };


        return $html . "</tr> \n";

    }

    public function getRows(){

        $html = "<tr> \n";

        foreach ($this->rows as  $r ){

            $html .= "<td> $r </td> \n";

        }

        $html .= "</tr> \n";

        return $html;

    }


    private function setHeader(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header("Content-Disposition: attachment; filename=\"{$this->filename}\"");
    }

    public function getHtml($tag = array('class' => 'table table-striped table-hover')){

        $ctag = '';

       foreach ($tag as $key => $value){
           $ctag .= "{$key}='{$value}'";
       }

       $html = "<table {$ctag}> \n";
       $html .= "<thead> \n";
       $html .= $this->getColumns();
       $html .= "</thead> \n <tbody> \n";
       $html .= $this->getRows();
       $html .= "<tbody> \n";
       $html .= '</table>';

       $this->setHeader();

       echo $html;

    }

}

