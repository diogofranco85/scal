<?php
/**
 * Created by PhpStorm.
 * User: diogofranco
 * Date: 29/12/18
 * Time: 00:16
 */

namespace Core;


class Report extends \FPDF{

    public $dir_base;
    public $title_report;
    public $username;

    public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4'){
        parent::__construct($orientation, $unit, $size);

        $this->AliasNbPages();
    }

    public function header(){
        $this->SetFont('Arial','B',16);
        $this->SetCreator('Diogo');
        $this->Image($this->dir_base.'/assets/images/logo_valorem_peq.png');
        $this->Ln();
        $this->SetFont('Arial', '','6');
        $this->MultiCell(30,10, 'GERADO EM: ',1,'L');
        $this->SetFont('Arial', 'B','8');
        $this->MultiCell(100,10,$this->title_report,1,'C');
        $this->SetFont('Arial', '','6');
        $this->MultiCell(30,10,"Usuario: \n" . $this->username, 1, 'L');


        $this->Ln(13);
    }

    public function footer(){

        $this->SetY(-15);
        $this->SetFont('Arial','I','8');
        $this->Cell(0,10,'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');

    }

}