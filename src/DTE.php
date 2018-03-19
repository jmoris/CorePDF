<?php

namespace SolucionTotal\CorePDF;

class DTE {

    private $html;
    private $pdf;

    public function __construct(){
        $this->pdf = new \Mpdf\Mpdf();
        $this->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($this->setCss(), 1);
        $this->html = '<div clas="factura">';
        $this->html .= $this->setInfoSuperior();
        $this->pdf->WriteHTML($this->html);
        $this->html .= '</div>';
    }

    public function generar(){
        $this->pdf->Output();
    }

    private function setCss(){
        $css = '
        .factura {
            width: 100%;
            margin: 2cm;
        }
        
        .info-emisor {
            width: 100%;
        }
        
        .info-emisor .logo{
            width: 30%;
            vertical-align: 60;
            display: inline-block;
        }
        
        .info-emisor .logo img {
            width: 100%;
            vertical-align: middle;
        }

        .info-emisor .info{
            width: 30%;
            vertical-align: top;
            display: inline-block;
        }
        
        .info-emisor .info .razonsocial {
            font-size: 10px;
            font-weight: bold;
            margin:0;
            padding:0;
        }
        
        .info-emisor .info .masinfo {
            font-size: 8px;
            margin:0;
            padding:0;
        }
        
        .info-emisor .cuadro{
            width: 30%;
            display: inline-block;
        }
        .info-emisor .cuadro .cuadro-rojo {
            font-size: 10px;
            border: 3px solid red;
            text-align: center;
        }
        ';
        return $css;
    }

    private function setInfoSuperior(){
        $html = '
            <div class="info-emisor">
                <div class="logo">
                    <img src="https://soluciontotal.s3.sa-east-1.amazonaws.com/contribuyentes/1/1.png">
                </div>
                <div class="info">'.$this->setEmisor().'</div>
                <div class="cuadro">'.$this->setCuadro().'</div>
            </div>
        ';
        return $html;
    }

    private function setEmisor(){
        $html = '
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
            <p style="font-size: 10px; text-align: center"><b>S.I.I. - CURICÓ</b></p>
        ';

        return $html;
    }

    private function setDetalle(){

    }

    private function setTotales(){

    }

    private function setAcuseRecibo(){

    }

    private function setTimbre(){

    }

}