<?php

  namespace App\Controller;
  
  use App\Model\ClientesModel;
  use App\Model\ContratosModel;
  use App\Model\EtiquetasModel;
  use App\Model\AmostrasModel;
  use \App\Src\MY_Controller;
  use App\Model\OperadorModel;
  use \Core\Database\Transaction;

  class EtiquetasController extends MY_Controller{

    public function __construct(){ 
      parent::__construct();
      $this->authenticate();
        
    }

    public function index(){

      Transaction::open('valorem');

      $etiquetas = new EtiquetasModel();
      $rs_etiquetas = $etiquetas->etiqueta_index();

      $clientes = new ClientesModel();
      $clientes->order('nmcliente');
      $clientes->where('ativo','=','S');
      $rs_clientes = $clientes->get();

      Transaction::close();

      $this->userData('clientes', $rs_clientes);
      $this->userData('etiquetas',$rs_etiquetas);
      $this->userData('page_title','Etiquetas');
      $this->render('etiquetas_index');
    }

    public function search(){

        Transaction::open('valorem');

        $contratos = new ContratosModel();
        $contratos->where('idcliente','=',$this->request->request->get('id'));
        $contratos->where('finalizado','=','N');
        $contratos->where('ativo','=','S');
        $rs = $contratos->get();


        $html = '';
        foreach($rs as $r){
            $html .= "<option value='".$r['id']."'>".$r['numcontrato']." / ".$r['hibrido']."</option> \n";
        }

        echo $html;

        Transaction::close();
    }

    public function gerar(){

        Transaction::open('valorem');

        $cont = $this->request->request->get('numetiquetas');
        $contrato = $this->request->request->get('idcontrato');
        $cliente = $this->request->request->get('idcliente');
        $setor = $this->request->request->get('setor');
        $cod_operador = $this->request->request->get('idoperador');

        $operadorModel = new OperadorModel();
        $operadorModel->where('cod','=',$cod_operador);
        $rs_operador = $operadorModel->get();
        $rs_operador = $rs_operador[0];

        $e = new EtiquetasModel();
        $e->fillable('max(numetiqueta) as cont');
        $e->where('idcontrato','=',$contrato);
        $e->where('idcliente','=',$cliente);
        $e->where('setor','=',$setor);
        $max = $e->get();

        if($max[0]['cont'] == null){
            $reg = 0;
        }else{
            $reg = $max[0]['cont'];
        }

        for($i = 1; $i <= $cont; $i++){

            $num_etiqueta = $reg+$i;
            $etiquetas = new EtiquetasModel();
            $etiquetas->idcliente = $cliente;
            $etiquetas->idcontrato = $contrato;
            $etiquetas->numetiqueta = $num_etiqueta;
            $etiquetas->setor = $setor;
            $etiquetas->idoperador = $rs_operador['id'];
            $etiquetas->created_at = date('Y-m-d h:i:s');
            $etiquetas->create();

        }

        echo json_encode(array('idcliente' => $cliente, 'idcontrato' => $contrato));

        Transaction::close();

    }

    public function viewAll($idcliente, $idcontrato){
        Transaction::open('valorem');

        $colunas = [
            'lab_etiquetas' => ['id,numetiqueta','setor'],
            'lab_cliente' => ['id as "idcliente",nmcliente, descricao'],
            'lab_contrato' => ['id as "idcontrato", hibrido, numcontrato'],
        ];

        $etiquetas = new EtiquetasModel();
        $etiquetas->fillable($colunas);
        $etiquetas->innerJoin('lab_cliente','id','idcliente');
        $etiquetas->innerJoin('lab_contrato','id','idcontrato');
        $etiquetas->where('lab_etiquetas.idcliente','=',$idcliente);
        $etiquetas->where('lab_etiquetas.idcontrato','=',$idcontrato);
        $etiquetas->where('lab_etiquetas.status','=','N');
        $prt_etiquetas = $etiquetas->get();

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'L',
            'format' => [26,55],
            'margin_left' => 1,
            'margin_right' => 1,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);




        $html = '<html>
        <head>
        <style>
            body {
                font-family: Tahoma;
                font-size: 1em;
                
                }
            .container{
                #border: solid 1px #000 ;
                padding: 2px;
                height: 2.0cm ;
                width: 4.5cm;
            }
            .dbarcode{
                margin-top: 0%;
                margin-bottom: 1px;
               
                padding: 2px;
                width: 100%;
                text-align: center;
                border: solid 1px #000 ;                
            }
            .dbarcode p{
                font-family: Courier;
                padding: 1px;
                font-size: 0.2rem;
            }
            .table{
                border-top: dotted 1px #000;
                border-bottom: dotted 1px #000;
                font-size: 0.3rem;
            }
            .table td{
                border-right: dotted 1px #000;
                border-left: dotted  1px #000;
                padding: 1px;
            }
            .table .tr_title td{
                    font-weight: bold;
                }
             .barcode{
                font-size: 0.8rem;
             }
        </style>
        </head>
        <body>
            <div class="container">
                <table width="100%" border="0">
                      <tr>
                        <td width="50%"><img src="{{url}}/img/logo_valorem_peq.png" height="12%"></td>
                        <td style="font-size: 0.5rem"><strong>Amostras</strong></td>
                        <td style="text-align: center; font-size: 0.5rem; background-color: #000; color: #fff;"><b>Nº:<br>{{setorresumido}}{{num}}</b></td>
                      </tr>
                </table>
                <div class="dbarcode">
                    <barcode code="{{barcode}}" type="C39" class="barcode" height="0.60" class="barcode"></barcode>
                    <p style="font-size: 6px; padding: 1px; margin: 1px">{{barcode}}</p>
                   
                </div>
                
                <table width="100%" cellpadding="0" cellspacing="0" class="table">
                    <tr class="tr_title">
                        <td width="33%">Cliente</td>
                        <td width="33%">Contrato</td>
                        <td>Setor</td>
                    </tr>
                    <tr>
                        <td style="font-size: 6px"><b>{{cliente}}</b></td>
                        <td style="font-size: 6px;"><b>{{contrato}}</b></td>
                        <td style="font-size: 6px;"><b>{{setor}}</b></td>    
                    </tr>
                </table>
            </div>
        </body>
        </html>';


        foreach ($prt_etiquetas as $pe){
            $html2 = $html;
            $barcode = $this->formatNumber($pe['id']);
            $num = $this->formatNumber($pe['numetiqueta']);
            
            $html2 = str_replace('{{num}}',$num,$html2);
            $html2 = str_replace('{{barcode}}',$barcode,$html2);
            $html2 = str_replace('{{cliente}}',$pe['descricao'],$html2);
            $html2 = str_replace('{{contrato}}',$pe['numcontrato'],$html2);
            $html2 = str_replace('{{hibrido}}',$pe['hibrido'],$html2);
            $html2 = str_replace('{{setorresumido}}',$pe['setor'],$html2);
            $html2 = str_replace('{{setor}}',$pe['setor'] == 'R' ? 'RECEBIMENTO' : 'TORRE',$html2);
            $html2 = str_replace('{{url}}',$this->getURL('/Assets'),$html2);
            $pdf->AddPage();
            $pdf->WriteHTML($html2);
        }
        $pdf->setTitle("SCAL - Etiquetas :: cliente [ {$pe['nmcliente']} ]");
        $pdf->Output();

        Transaction::close();
    }

    private function formatNumber($number){
        if($number < 10){
            $n = "000{$number}";
        }elseif($number < 100 || $number >= 10){
            $n = "00{$number}";
        }elseif($number < 1000 || $number >= 100){
            $n = "0{$number}";
        }else{
            $n = $number;
        }

        return $n;

    }

    public function listview($idcliente, $idcontrato){
        Transaction::open('valorem');

            $colunas = [
                'lab_etiquetas' => ['id', 'lotefinal', 'numetiqueta', 'status','setor'],
                'lab_contrato' => ['hibrido','numcontrato'],
                'lab_operador' => ['name'],
                'lab_cliente' => ['descricao'],
            ];

            $etiquetas = new EtiquetasModel();
            $etiquetas->fillable($colunas);
            $etiquetas->innerJoin('lab_cliente', 'id', 'idcliente');
            $etiquetas->innerJoin('lab_contrato','id','idcontrato');
            $etiquetas->innerJoin('lab_operador','id','idoperador');
            $etiquetas->leftJoin('lab_amostra','id','idamostra');
            $etiquetas->where('lab_etiquetas.ativo','=','S');
            $etiquetas->where('lab_etiquetas.idcliente','=',$idcliente);
            $etiquetas->where('lab_etiquetas.idcontrato','=',$idcontrato);
            //$etiquetas->group('lab_etiquetas.setor, lab_etiquetas.numetiqueta');
            //$etiquetas->order('lab_etiquetas.id ASC');

            $rs_etiquetas = $etiquetas->get(false, true);

            $this->userData('etiquetas',$rs_etiquetas);
            $this->userData('page_title','Etiquetas geradas');
            $this->render('etiquetas_list');

        Transaction::close();

    }

    public function printer($id){

        Transaction::open('valorem');
        $rs_etiquetas = EtiquetasModel::find($id);
        $rs_cliente = ClientesModel::find($rs_etiquetas->idcliente);
        $rs_contrato = ContratosModel::find($rs_etiquetas->idcontrato);

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'L',
            'format' => [26,55],
            'margin_left' => 1,
            'margin_right' => 1,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        $html = '<html>
        <head>
        <style>
            body {
                font-family: Tahoma;
                font-size: 1em;
                
                }
            .container{
                #border: solid 1px #000 ;
                padding: 2px;
                height: 2.0cm ;
                width: 4.5cm;
            }
            .dbarcode{
                margin-top: 0%;
                margin-bottom: 1px;
               
                padding: 2px;
                width: 100%;
                text-align: center;
                border: solid 1px #000 ;                
            }
            .dbarcode p{
                font-family: Courier;
                padding: 1px;
                font-size: 0.2rem;
            }
            .table{
                border-top: dotted 1px #000;
                border-bottom: dotted 1px #000;
                font-size: 0.3rem;
            }
            .table td{
                border-right: dotted 1px #000;
                border-left: dotted  1px #000;
                padding: 1px;
            }
            .table .tr_title td{
                    font-weight: bold;
                }
             .barcode{
                font-size: 0.8rem;
             }
        </style>
        </head>
        <body>
            <div class="container">
                <table width="100%" border="0">
                      <tr>
                        <td width="50%"><img src="Assets/img/logo_valorem_peq.png" height="12%"></td>
                        <td style="font-size: 0.5rem"><strong>Amostras</strong></td>
                        <td style="text-align: center; font-size: 0.4rem; background-color: #000; color: #fff;"><b>Nº:<br>{{setorresumido}}{{num}}</b></td>
                      </tr>
                </table>
                <div class="dbarcode">
                    <barcode code="{{barcode}}" type="C39" class="barcode" height="0.60" class="barcode"></barcode>
                   <p style="font-size: 0.3rem; padding: 1px; margin: 1px">{{barcode}}</p>
                </div>
                
                <table width="100%" cellpadding="0" cellspacing="0" class="table">
                    <tr class="tr_title">
                        <td width="33%">Cliente</td>
                        <td width="33%">Contrato</td>
                        <td>Setor</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;">{{cliente}}</td>
                        <td style="font-size: 12px">{{contrato}}</td>
                        <td style="font-size: 12px">{{setor}}</td>    
                    </tr>
                </table>
            </div>
        </body>
        </html>';

        $barcode = $this->formatNumber($rs_etiquetas->id);
        $num = $this->formatNumber($rs_etiquetas->numetiqueta);

        $html = str_replace('{{num}}',$num,$html);
        $html = str_replace('{{barcode}}',$barcode,$html);
        $html = str_replace('{{cliente}}',$rs_cliente->descricao,$html);
        $html = str_replace('{{contrato}}',$rs_contrato->numcontrato,$html);
        $html = str_replace('{{hibrido}}',$rs_contrato->hibrido,$html);
        $html = str_replace('{{setorresumido}}',$rs_etiquetas->setor,$html);
        $html = str_replace('{{setor}}','CP'.$rs_etiquetas->setor == 'R' ? 'RECEBIMENTO' : 'TORRE' ,$html);

        $pdf->AddPage();
        $pdf->WriteHTML($html);
        $pdf->setTitle("SCAL - Etiquetas :: cliente [ {$rs_cliente->nmcliente} ]");
        $pdf->Output();



        Transaction::close;

    }

    public function validarEtiqueta(){
        $numetiqueta = $this->request->request->get('etiqueta');

        Transaction::open('valorem');

        $coluna = [
            'lab_etiquetas' => ['id','idcliente','idamostra'],
            'lab_cliente' => ['nmcliente'],
            'lab_contrato' => ['numcontrato','hibrido']
        ];

        $etiqueta = new EtiquetasModel();
        $etiqueta->fillable($coluna);
        $etiqueta->innerJoin('lab_cliente','id','idcliente');
        $etiqueta->innerJoin('lab_contrato','id','idcontrato');
        $etiqueta->where('lab_etiquetas.id','=',$numetiqueta);
        $rs_etiqueta = $etiqueta->get();


        if(count($rs_etiqueta) > 0){
            $amostra = AmostrasModel::all("numetiqueta = {$numetiqueta}");
            if(count($amostra) > 0){
                echo json_encode([ 'code' => 200, 'data' => $rs_etiqueta[0]]);
            }else{
                echo json_encode([ 'code' => 404, 'message' => 'Etiqueta não possui amostra vinculada']);
            }
            
        }else{
            echo json_encode([ 'code' => 404, 'message' => 'Etiqueta não foi localizada']);
        }

        Transaction::close();

    }

  }