<?php

namespace SolucionTotal\CorePDF;
use Mpdf;


class DTE extends Mpdf {

    private $html;


    public function __construct(){
        $this->html = '';
        $this->html .= $this->setCss();
        $this->html .= $this->setInfoSuperior();
        $this->WriteHTML($this->html);
    }

    public function generar(){
        $this->Output();
    }

    private function setCss(){
        $css = '
            <style>
                .factura {
                    font-size: 8pt;
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
                    width: 30%;
                    display: inline-block;
                }

                .info-emisor .info {
                    width: 40%;
                    display: inline-block;
                }

                .info-emisor .cuadro {
                    width: 30%;
                    display: inline-block;
                }

                .espacio {
                    padding-bottom: 5px;
                }
            </style>
        ';
        return $css;
    }

    private function setInfoSuperior(){
        $html = '
            <div class="info-emisor">
                <div class="logo"></div>
                <div class="info"></div>
                <div class="cuadro">'.$this->setCuadro().'</div>
            </div>
        ';
        return $html;
    }

    private function setEmisor(){

    }

    private function setCuadro(){
        $html = '
            <div style="line-height: 0.7; heigth: 20px; font-size:10px; text-align: center; border-bottom-style: solid; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-width: 1px;">
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