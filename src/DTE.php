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
                    font-size: 8pt;
                }

                .razonsocial {
                    margin: 0px;
                    padding: 0px;
                    font-weight: bold;
                }
                
                .masinfo {
                      margin: 0px;
                      padding: 0px;
                      font-size: 10px;
                }

                .bordes {
                    border-left: 1px solid black;
                    border-right: 1px solid black;
                    border-top: 1px solid black;
                    border-bottom: 1px solid black;
                }

                .tabla-header {
                    width: 100%;
                }

                .detalles {
                    text-align: center;
                    width: 100%;
                }

                .detalles tr th {
                    border-left: 1px solid black;
                    border-right: 1px solid black;
                    border-top: 1px solid black;
                    border-bottom: 1px solid black;
                    font-weight: bold;
                } 

                .detalles tr td {
                    border-right: 1px solid black;
                }

                .info-emisor {
                    width: 100%;
                    height: 100px;
                }

                .info-emisor .logo {
                    display: inline-block;
                    width: 28%;
                }

                .info-emisor .logo img {
                    position:absolute;
                    top:50%;
                    left:50%;
                    width: 100%;
                }

                .info-emisor .info {
                    display: inline-block;
                    width: 40%;
                }

                .info-emisor .cuadro {
                    display: inline-block;
                    width: 30%;
                }

                .info-emisor .cuadro .cuadro-rojo {
                    border-left: 1px solid red;
                    border-right: 1px solid red;
                    border-top: 1px solid red;
                    border-bottom: 1px solid red;
                }

                .espacio {
                    padding-bottom: 5px;
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