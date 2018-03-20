<?php

namespace SolucionTotal\CorePDF;

class DTE {

    private $html;
    private $pdf;

    public function __construct(){
        $this->pdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $this->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($this->setCss(), 1);
        $this->html = '<div class="factura">';
        $this->html .= $this->setInfo();
        $this->pdf->WriteHTML($this->html);
        $this->html .= '</div>';
    }

    public function generar(){
        $this->pdf->Output();
    }

    private function setCss(){
        $css = '
        .factura {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 8px;
            width: 100%;
        }
        
        .info-emisor {
            width: 100%;
        }
        
        .info-emisor .logo{
            width: 7cm;
            height: 3cm;
            float:left;
        }
        
        .info-emisor .logo img {
            width: 100%;
            vertical-align: middle;
        }

        .info-emisor .info{
            margin-left: 10px;
            width: 6cm;
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
            font-size: 8px;
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
            font-size: 8px;
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        .referencias table th {
            border: 1px solid black;
        }

        .referencias table td {
            border-right: 1px solid black;
        }

        .detalle {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 8px;
            font-weight: bold;
        }

        .detalle table {
            border-collapse: collapse;
            empty-cells: show;
            border: 1px solid black;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 8px;
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        .detalle table th {
            border: 1px solid black;
        }

        .detalle table td {
            border-right: 1px solid black;
        }

        .emisor table {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 8px;
            font-weight: bold;
            width: 100%;
        }

        .emisor table td {
            width: 25%;
        }

        .info-emisor .cuadro{
            width: 6.5cm;
            float:right;
            font-size: 14px;
            line-height: 0.7;
            color: red;
            text-align: center;
        }
        .info-emisor .cuadro .cuadro-rojo {
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
                    '.$this->setEmisor().'
                </div>
                <div class="info"></div>
                <div class="cuadro">'.$this->setCuadro().'</div>
            </div>
            <div class="receptor">'.$this->setReceptor().'</div>
            <div class="referencias">'.$this->setReferencias().'</div>
            <div class="espacio-2"></div>
            <div class="detalle">'.$this->setDetalle().'</div>
        ';
        return $html;
    }

    private function setEmisor(){
        $html = '
            <div class="espacio-5"></div>
            <p class="razonsocial">JESUS EDUARDO MORIS HERNANDEZ</p>
            <p class="masinfo">SERVICIOS INTEGRALES DE INFORMATICA</p>
            <p class="masinfo">LAS ARAUCARIAS #25, TENO, CURICO</p>
            <p class="masinfo">Email: contacto@soluciontotal.cl</p>
        ';
        return $html;
    }

    private function setCuadro(){
        $html = '
            <div class="cuadro-rojo">
                <p><b>R.U.T.: 19.587.757-2</b></p>
                <p><b>FACTURA ELECTRONICA</b></p>
                <p><b>Nº 1000</b></p>
            </div>
            <p><b>S.I.I. - CURICÓ</b></p></div>
        ';

        return $html;
    }

    private function setReceptor(){
        $html = '
            <table class="bordes">
                <tbody>
                    <tr>
                        <td>SEÑOR(ES)</td>
                        <td>:</td>
                        <td>FECHA EMISION</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>DIRECCION</td>
                        <td>:</td>
                        <td>FECHA VENCIMIENTO</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>COMUNA</td>
                        <td>:</td>
                        <td>CIUDAD</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>GIRO</td>
                        <td>:</td>
                        <td>R.U.T.</td>
                        <td>:</td>                        
                    </tr>
                    <tr>
                        <td>MEDIO DE PAGO</td>
                        <td>:</td>
                        <td>TELEFONO</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>CONDICION DE PAGO</td>
                        <td>:</td>
                        <td>PATENTE</td>
                        <td>:</td>
                    </tr>
                </tbody>
            </table>
        ';
    }

    private function setReferencias(){
        $html = '
            <p class="texto">Documentos referenciados</p>
            <table>
                <thead>
                    <tr>
                        <th>Tipo documento</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Factura electronica</td>
                        <td>100</td>
                        <td>23/02/2019</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        ';
    }

    private function setDetalle(){
        $html = '
            <table>
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Detalle</th>
                        <th>P. Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10</td>
                        <td>Kg</td>
                        <td>Articulo de prueba 1</td>
                        <td>$ 990</td>
                        <td>$ 9.900</td>
                    </tr>
        ';
        for($i = 0; $i < 25; $i++){
            $html .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            ';
        } 
        $html .= '
                </body>
            </table>
        ';
    }

    private function setTotales(){

    }

    private function setAcuseRecibo(){

    }

    private function setTimbre(){

    }

}