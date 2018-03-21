<?php

namespace SolucionTotal\CorePDF;

class DTE {

    private $html;
    private $pdf;
    private $acuse = false;

    public function __construct($acuse){

        $this->acuse = $acuse;

        $this->pdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $this->pdf->SetDisplayMode('fullpage');
        $this->html = '<head>
                        <style>';
        $this->html .= $this->setCss();
        $this->html .= '</style>
                        </head>
                        <body><div class="factura">';
        $this->html .= $this->setInfo();
        $this->html .= '</div></body>';
        $this->pdf->WriteHTML($this->html);
    }

    public function generar(){
        $this->pdf->Output();
    }

    private function setCss(){
        $css = '
        @page {
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-header: 8mm;
            margin-footer: 8mm;
            background-color:#ffffff;
        }

        .factura {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            width: 100%;
        }
        
        .info-emisor {
            width: 100%;
        }
        
        .info-emisor .logo{
            width: 10cm;
            height: 3cm;
            float:left;
        }
        
        .info-emisor .logo img {
            width: 100%;
            vertical-align: middle;
        }

        .info-emisor .info{
            margin-left: 10px;
            vertical-align: top;
            float:left;
        }
        
        .info-emisor .logo .razonsocial {
            font-size: 14px;
            font-weight: bold;
            margin:0;
            padding:0;
        }
        
        .info-emisor .logo .masinfo {
            font-size: 10px;
            font-weight: normal;
            margin:0;
            padding:0;
        }
        
        .bordes {
            border: 1px solid black;
        }

        .referencias {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            font-weight: bold;
        }

        .referencias .texto {
            padding: 0;
            margin: 0;
        }

        .referencias table {
            border-collapse: collapse;
            empty-cells: show;
            border: 1px solid black;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            font-weight: normal;
            width: 100%;
            text-align: center;
        }

        .referencias table th {
            font-weight: bold;
            border: 1px solid black;
        }

        .referencias table td {
            border-right: 1px solid black;
        }

        .detalle {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            font-weight: bold;
        }

        .detalle table {
            border-collapse: collapse;
            empty-cells: show;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            font-weight: normal;
            width: 100%;
        }

        .detalle table th {
            border: 1px solid black;
            font-weight: bold;
        }
        
        .detalle table .producto {
            border-bottom: 1px solid black;
        }

        .detalle table .fin-producto {
            padding-bottom: 10px;
        }

        .detalle table .producto td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .detalle table .producto {
            border-right: 1px solid black;
            text-align: right;
        }

        .detalle table .timbre {
            border: 1px solid black;
        }

        .detalle table .timbre td {
            padding-left: 1cm;
        }

        .timbre-texto {
            width: 8cm;
            text-align: center;
        }

        .detalle table .producto .fintd {
            border-right: 0 solid black;
        }

        .detalle table .fin-producto td {
            padding-top: 1px;
        }
        
        .detalle table .producto .numero {
            padding-right: 5px;
            text-align: right;
        }

        .detalle .total {
            text-align: right; 
            padding-right: 5px; 
            border-left: 1px solid black; 
            border-right: 1px solid black;
            padding-bottom: 5px;
            font-size: 10px;
        }

        .detalle table .titulo {
            font-weight: bold;
        }

        .detalle table .valor {
            font-weight: normal;
        }
            
        .receptor table {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            width: 100%;
        }

        .receptor table .valor{
            width: 30%;
            font-weight: normal;
        }

        .receptor table .titulo{
            width: 15%;
            font-weight: bold;
        }

        .info-emisor .cuadro{
            width: 6.5cm;
            float:right;
            font-size: 14px;
            color: red;
            text-align: center;
        }

        .info-emisor .cuadro .cuadro-rojo {
            padding-top: 0;
            padding-bottom: 0;
            border: 3px solid red;
        }

        .espacio-5 {
            margin: 5px;
        }

        .espacio-2 {
            margin: 2px;
        }
        ';
        return $css;
    }

    private function setInfo(){
        $html = '
            <div class="info-emisor">
                <div class="logo">
                    <img src="https://soluciontotal.s3.sa-east-1.amazonaws.com/contribuyentes/1/1.png">
                    <div class="espacio-5"></div>
                    '.$this->setEmisor().'
                </div>
                <div class="info"></div>
                <div class="cuadro">'.$this->setCuadro().'</div>
            </div>
            <div class="espacio-5"></div>
            <div class="receptor">'.$this->setReceptor().'</div>
            <div class="referencias">'.$this->setReferencias().'</div>
            <div class="espacio-2"></div>
            <div class="detalle">'.$this->setDetalle().'</div>
            <div class="espacio-5"></div>
            '.(($this->acuse) ? $this->setAcuseRecibo() : '');
        return $html;
    }

    private function setEmisor(){
        $html = '
            <p class="razonsocial">JESÚS EDUARDO MORIS HERNÁNDEZ </p>
            <p class="masinfo">SERVICIOS INTEGRALES DE INFORMATICA</p>
            <p class="masinfo">LAS ARAUCARIAS #25, TENO, CURICO</p>
            <p class="masinfo">Telefono: (75) 2 412479</p>
            <p class="masinfo">Email: contacto@soluciontotal.cl</p>
            <p class="masinfo">Web: www.soluciontotal.cl</p>
        ';
        return $html;
    }

    private function setCuadro(){
        $html = '
            <div class="cuadro-rojo">
                <p><b>R.U.T.: 19.587.757-2</b></p>
                <p><b>GUÍA DE DESPACHO ELECTRÓNICA</b></p>
                <p><b>Nº 1000</b></p>
            </div>
            <p style="margin:0;padding:0;"><b>S.I.I. - CURICÓ</b></p></div>
        ';

        return $html;
    }

    private function setReceptor(){
        $html = '
            <table class="bordes">
                <tbody>
                    <tr>
                        <td class="titulo">SEÑOR(ES)</td>
                        <td>: EDUAROD JAIME MORIS OLIVARES</td>
                        <td class="titulo">FECHA EMISION</td>
                        <td>: 20-03-2018</td>
                    </tr>
                    <tr>
                        <td class="titulo">DIRECCION</td>
                        <td>: LAS ARAUCARIAS #25</td>
                        <td class="titulo">FECHA VENCIMIENTO</td>
                        <td>: 20-03-2018</td>
                    </tr>
                    <tr>
                        <td class="titulo">COMUNA</td>
                        <td>: TENO</td>
                        <td class="titulo">CIUDAD</td>
                        <td>: CURICO</td>
                    </tr>
                    <tr>
                        <td class="titulo">GIRO</td>
                        <td>: CASINO</td>
                        <td class="titulo">R.U.T.</td>
                        <td>: 16.262.265-K</td>                        
                    </tr>
                    <tr>
                        <td class="titulo">MEDIO DE PAGO</td>
                        <td>:</td>
                        <td class="titulo">TELEFONO</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td class="titulo">CONDICION DE PAGO</td>
                        <td>: CREDITO</td>
                        <td class="titulo">PATENTE</td>
                        <td>:</td>
                    </tr>
                </tbody>
            </table>
        ';
        return $html;
    }

    private function setReferencias(){
        $html = '
            <div class="espacio-5"></div>
            <table>
                <thead>
                    <tr>
                        <th align="left">TIPO DOCUMENTO</th>
                        <th>FOLIO</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="left">Factura electronica</td>
                        <td>100</td>
                        <td>23/02/2019</td>
                    </tr>
                    <tr>
                        <td align="left">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="left">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        ';
        return $html;
    }

    private function setDetalle(){
        $html = '
            <table>
                <thead>
                    <tr>
                        <th width="10%">CANTIDAD</th>
                        <th width="10%">UNIDAD</th>
                        <th width="50%">DETALLE</th>
                        <th width="15%">P. UNITARIO</th>
                        <th width="15%" class="fintd">TOTAL</th>
                    </tr>
                </thead>
                <tbody>';
                    for($i = 0; $i < 30; $i++){
                        $html.='<tr class="producto">
                        <td class="numero">'.($i+1).'</td>
                        <td style="text-align: center">Kg</td>
                        <td>Articulo de prueba 1</td>
                        <td class="numero">990</td>
                        <td class="numero">9.900</td>
                    </tr>';
                    }
                    $html.='<tr>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;" colspan="3"></td>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;"></td>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="border-left: 0; border-bottom: 0; padding-right: 70px; text-align: center;" rowspan="6" colspan="3">
                            '.$this->setTimbre().'
                            <p>Timbre Electronico SII</p>
                            <p>Resolucion 0 de 2014</p>
                            <p>Verifique documento: www.sii.cl</p>
                        </td>
                        <td style="padding-top: 5px;" class="total titulo">SUBTOTAL</td>
                        <td class="total valor">$0</td>
                    </tr>
                    <tr>
                        <td class="total titulo">DESCUENTO</td>
                        <td class="total valor">$0</td>
                    </tr>
                    <tr>
                        <td class="total titulo">EXENTO</td>
                        <td class="total valor">$0</td>
                    </tr>
                    <tr>
                        <td class="total titulo">NETO</td>
                        <td class="total valor">$0</td>
                    </tr>
                    <tr>
                        <td class="total titulo">I.V.A</td>
                        <td class="total valor">$0</td>
                    </tr>
                    <tr>
                        <td class="total titulo" style="border-bottom: 1px solid black">TOTAL</td>
                        <td class="total valor" style="border-bottom: 1px solid black">$0</td>
                    </tr>
                </tbody>
            </table>
        ';
        return $html;
    }

    private function setTotales(){

    }

    private function setAcuseRecibo(){
        $html = '
            <table style="border: 1px solid black;">
                <tr>
                    <td style="padding-left: 5px; padding-top: 10px;" width="10%">Nombre</td>
                    <td style="padding-top: 10px" width="40%">: _____________________________________________________</td>
                    <td style="padding-top: 10px" width="10%">RUT</td>
                    <td style="padding-top: 10px" width="40%">: _____________________________________________________</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px; padding-top: 10px; padding-bottom: 5px" width="10%">Fecha</td>
                    <td style="padding-top: 10px" width="40%">: _____________________________________________________</td>
                    <td style="padding-top: 10px"style="padding-top: 5px; padding-bottom: 5px" width="10%">Recinto</td>
                    <td style="padding-top: 10px" width="40%">: _____________________________________________________</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px;" width="80%" colspan="3">El acuse de recibo que se declara en este acto, de acuerdo a lo dispuesto en la letra b)
                    del Art. 4°, y la letra c) del Art. 5° de la Ley 19.983, acredita que la entrega de mercaderías
                    o servicio (s) prestado (s) ha (n) sido recibido (s).</td>
                    <td style="padding-left: 10px"><b>Firma:</b> ____________________________________________</td>
                </tr>
            </table>
            <div align="right" style="font-size: 14px">CEDIBLE</div>
        ';
        return $html;
    }

    private function setTimbre(){
        $pdf417 = new \Com\Tecnick\Barcode\Barcode();
        $ecl = version_compare(phpversion(), '7.0.0', '<') ? -1 : 5;
        $bobj = $pdf417->getBarcodeObj(
        'PDF417,,'.$ecl,                     
        '<TED></TED>',
        -1,
        -1,
        'black',
        array(0, 0, 0, 0)
        )->setBackgroundColor('white');

        $timbre = '<img style="width: 8cm; height: 2.5cm;"src="data:image/png;base64,'.base64_encode($bobj->getPngData()).'">';

        return $timbre;
    }

}