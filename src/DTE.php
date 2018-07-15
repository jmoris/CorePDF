<?php

namespace SolucionTotal\CorePDF;

class DTE {

    private $html;
    private $pdf;
    private $formato = false;
    private $dte;
    private $ted;
    private $resolucion = [1970, 0];
    private $cedible = false;
    private $logo;
    private $poslogo;
    private $telefono, $mail, $web;
    private $copias;
    private $subtotal;

    private $no_cedible = [0, 61, 56, 39];
    private $tipo_dte = [
        0 => 'COTIZACIÓN',
        33 => 'FACTURA ELECTRÓNICA',
        34 => 'FACTURA NO AFECTA O EXENTA ELECTRÓNICA',
        39 => 'BOLETA ELECTRÓNICA',
        52 => 'GUÍA DE DESPACHO ELECTRÓNICA',
        56 => 'NOTA DE DÉBITO ELECTRÓNICA',
        61 => 'NOTA DE CRÉDITO ELECTRÓNICA',
        801 => 'ORDEN DE COMPRA',
        802 => 'NOTA DE PEDIDO'
    ];

    private $cols = [
        'CANTIDAD' => ['width' => 10],
        'UNIDAD' => ['width' => 10],
        'DETALLE' => ['width' => 48],
        'P. UNITARIO' => ['width' => 12],
        'DSCTO' => ['width' => 8],
        'TOTAL' => ['width' => 12]
    ];

    public function __construct(array $DTE, $formato, $logo, $ted = null, $cedible = false, $poslogo = 1, $copias = true){
        $this->dte = $DTE;
        $this->logo = $logo;
        $this->ted = $ted;
        $this->cedible = $cedible;
        $this->poslogo = $poslogo;
        $this->copias = $copias;
        $this->formato = $formato;
        $this->subtotal = 0;
        if(!$formato){
            $this->pdf = new \Mpdf\Mpdf(['format' => 'A4']);
            $this->pdf->SetCompression(true); // forzamos la compresion del PDF
            $this->pdf->SetDisplayMode('fullpage');
        }else{
            $this->pdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [72, 1000], 'setAutoTopMargin' => 'pad']);
            $this->pdf->SetCompression(true); // forzamos la compresion del PDF
            $this->pdf->SetDisplayMode('fullpage');
        }
    }

    public function construir(){
        $this->html = '<head>
                        <style>';
        $this->html .= (!$this->formato)?$this->setCss():$this->setCssPOS();
        $this->html .= '</style>
                        </head>
                        <body>';
                        
        $this->dteh = '<div class="dte">';
        $this->dteh .= (!$this->formato)?$this->setInfo(false, $this->poslogo):$this->setInfoPOS(false);
        $this->dteh .= '</div>';
        $this->html .= $this->dteh;
        $this->html .= '</body>';
        $this->pdf->WriteHTML($this->html);   
        if($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 39){
            if($this->copias){
                $this->pdf->AddPage();
                $this->pdf->WriteHTML($this->html);
            }
            if($this->cedible){
                $this->pdf->AddPage();
                $this->html = '<head>
                                <style>';
                $this->html .= (!$this->formato)?$this->setCss():$this->setCssPOS();
                $this->html .= '</style>
                            </head>
                            <body>
                                <div class="dte">';
                $this->html .= (!$this->formato)?$this->setInfo(true, $this->poslogo):$this->setInfoPOS(true);
                $this->html .= '</div>
                            </body>';
                $this->pdf->WriteHTML($this->html);     
            }          
        }
    }

    public function generar($descarga = 0, $filename = 'dte.pdf'){
        $path = 'ST_'.$this->dte['Encabezado']['IdDoc']['TipoDTE'].'F'.$this->dte['Encabezado']['IdDoc']['Folio'].'.pdf';
        if($descarga == 0){
            $this->pdf->Output($path, \Mpdf\Output\Destination::DOWNLOAD);
         }else if($descarga == 1){ 
            $this->pdf->Output($path, \Mpdf\Output\Destination::INLINE);
         }else if($descarga == 2){
            $this->pdf->Output($filename, \Mpdf\Output\Destination::FILE);
         }
    }

    private function getTipo($tipo){
        if (!is_numeric($tipo) and !isset($this->tipo_dte[$tipo]))
            return $tipo;
        return isset($this->tipo_dte[$tipo]) ? strtoupper($this->tipo_dte[$tipo]) : 'Documento '.$tipo;
    }

    public function setResolucion($ano, $nro){
        $this->resolucion[0] = $ano;
        $this->resolucion[1] = $nro;
    }

    private function getResolucion(){
        return 'Resolución '.$this->resolucion[1].' de '.$this->resolucion[0];
    }

    public function setTelefono($telefono){
        if($telefono != ''){
            $this->telefono = '<p class="masinfo">Telefono: '.$telefono.'</p>';
        }
    }

    public function setMail($mail){
        if($mail != ''){
            $this->mail = '<p class="masinfo">Mail: '.$mail.'</p>';
        }
    }

    public function setWeb($web){
        if($web != ''){
            $this->web = '<p class="masinfo">Web: '.$web.'</p>';
        }
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

        .dte {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            width: 100%;
        }

        .acuse-recibo {
            border: 1px solid black;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            width: 100%;
        }
        
        .texto-cedible {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 14px;
            text-align: right;
            font-weight: bold;
        }

        .info-emisor {
            width: 100%;
        }
        
        .info-emisor .logo-10w {
            width: 10cm;
            height: 3cm;
            float:left;
        }

        .info-emisor .logo-5w {
            width: 4cm;
            height: 3cm;
            float:left;
        }

        .info-emisor .info{
            width: 8cm;
            margin-left: 10px;
            vertical-align: top;
            float:left;
        }
        
        .info-emisor .razonsocial {
            font-size: 14px;
            font-weight: bold;
            margin:0;
            padding:0;
        }
        
        .info-emisor .masinfo {
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

    private function setCssPOS(){
        $css = '.dte { 
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                }
                .cuadro {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    text-align: center;
                }
                .cuadro .sucursal {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    line-height: 0.1;
                }
                .logo {
                    width: 80%;
                    margin: auto;
                }
                .total{
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    margin-top: 5px;
                    width: 100%;
                }
                .total .derecha {
                    text-align: right;
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%;
                }
                .total .izquierda {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    text-align: left;
                }
                .total .margen-izq {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%;
                    padding-left: 10px;
                }
                .bordes {
                    border: 3px solid black;
                }
                .emisor {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    line-height: 0.1;
                    margin-left: 5px;
                }
                .receptor {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    line-height: 0.1;
                    margin-left: 5px;
                }
                .wrap {
                    height: 5px;    
                }
                .wrap-min {
                    height: 1px;
                }
                .codigo {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    margin-top: .5cm;
                    text-align: center;
                    line-height: 0.2;
                }
                .tabla {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    width: 100%;
                    text-align: center;
                }

                .tabla th {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                }

                .tabla td {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                }

                .tabla2 {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    width: 100%;
                }
                .tabla2 th {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    text-align: center;
                }
                .tabla2 td {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 80%; 
                    font-size: 8px;
                    text-align: center;
                }
                @page {
                    width: 72mm;
                    margin-top: .2cm;
                    margin-bottom: .5cm;
                    margin-left: .1cm;
                    margin-right: .1cm;
                    margin-header: 2mm;
                    margin-footer: 10mm;
                    background-color:#ffffff;
            }';
        return $css;
    }

    public function getHTML(){
        return $this->html;
    }

    private function setInfo($acuse = false, $poslogo = 1){
        $txtacuse = '';
        if($acuse)
            if(!array_key_exists($this->dte['Encabezado']['IdDoc']['TipoDTE'], $this->no_cedible))
                $txtacuse = $this->setAcuseRecibo();

        $logo = '';
        if($poslogo == 1){
            $logo = '<div class="logo-10w">
                        <img src="'.$this->logo.'">
                        <div class="espacio-5"></div>
                        '.$this->setEmisor().'
                    </div>';
        }else if($poslogo == 2){
            $logo = '<div class="logo-5w">
                        <img src="'.$this->logo.'">
                    </div>
                    <div class="info">'.$this->setEmisor().'</div>';     
        }   
        $html = '
            <div class="info-emisor">
                '.$logo.'
                <div class="cuadro">'.$this->setCuadro().'</div>
            </div>
            <div class="espacio-5"></div>
            <div class="receptor">'.$this->setReceptor().'</div>
            <div class="referencias">'.$this->setReferencias().'</div>
            <div class="espacio-2"></div>
            <div class="detalle">'.$this->setDetalle().'</div>
            <div class="espacio-5"></div>
            '.$txtacuse;
        return $html;
    }

    private function formatRut($rut){
        $rutE = explode('-', $rut);
        $primero = substr($rutE[0], 0, 2);
        $fin = 2;
        if(strlen($rutE[0]) == 7){
            $primero = substr($rutE[0], 0, 1);
            $fin = 1;
        }
        $rutF = $primero.'.'.substr($rutE[0], $fin, 3).'.'.substr($rutE[0], $fin+3, 3).'-'.$rutE[1];
        return $rutF;
    }

    private function formatNumber($numero){
        return number_format(intval($numero), 0, ",", ".");
    }

    private function setEmisor(){
        $html = '
            <p class="razonsocial">'.$this->dte['Encabezado']['Emisor']['RznSoc'].'</p>
            <p class="masinfo">'.$this->dte['Encabezado']['Emisor']['GiroEmis'].'</p>
            <p class="masinfo">'.$this->dte['Encabezado']['Emisor']['DirOrigen'].', '.$this->dte['Encabezado']['Emisor']['CmnaOrigen'].'</p>
            '.$this->telefono.'
            '.$this->mail.'
            '.$this->web.'
        ';
        return $html;
    }

    private function setCuadro(){
        $inferior = ($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 0) ? '<p style="margin:0;padding:0;"><b>S.I.I. - '.\SolucionTotal\CorePDF\SII::getDireccionRegional($this->dte['Encabezado']['Emisor']['CmnaOrigen']).'</b></p></div>':'';
        $html = '
            <div class="cuadro-rojo">
                <p><b>R.U.T.: '.$this->formatRut($this->dte['Encabezado']['Emisor']['RUTEmisor']).'</b></p>
                <p><b>'.$this->getTipo($this->dte['Encabezado']['IdDoc']['TipoDTE']).'</b></p>
                <p><b>Nº '.$this->dte['Encabezado']['IdDoc']['Folio'].'</b></p>
            </div>
            '.$inferior;

        return $html;
    }

    private function setReceptor(){
        $textoguia = [
            'TIPO TRASLADO',
            'TIPO DESPACHO'
        ];
        $valorguia = [
            (!isset($this->dte['Encabezado']['IdDoc']['IndTraslado']))?'':\SolucionTotal\CorePDF\SII::getTipoTraslado($this->dte['Encabezado']['IdDoc']['IndTraslado']),
            (!isset($this->dte['Encabezado']['IdDoc']['TipoDespacho']))?'':\SolucionTotal\CorePDF\SII::getTipoDespacho($this->dte['Encabezado']['IdDoc']['TipoDespacho'])
        ];
        $textodoc = [
            'MEDIO DE PAGO',
            'CONDICION DE PAGO'
        ];
        $valordoc = [
            (!isset($this->dte['Encabezado']['IdDoc']['MedioPago']))?'':$this->dte['Encabezado']['IdDoc']['MedioPago'],
            (!isset($this->dte['Encabezado']['IdDoc']['FmaPago']))?'':\SolucionTotal\CorePDF\SII::getFormaPago($this->dte['Encabezado']['IdDoc']['FmaPago'])
        ]; 
        $opctexto = ($this->dte['Encabezado']['IdDoc']['TipoDTE']!=52) ? $textodoc : $textoguia;
        $opcvalor = ($this->dte['Encabezado']['IdDoc']['TipoDTE']!=52) ? $valordoc : $valorguia;
        $fecha_emision = date('d-m-Y', strtotime($this->dte['Encabezado']['IdDoc']['FchEmis']));
        $html = '
            <table class="bordes">
                <tbody>
                    <tr>
                        <td class="titulo">SEÑOR(ES)</td>
                        <td>: '.$this->dte['Encabezado']['Receptor']['RznSocRecep'].'</td>
                        <td class="titulo">R.U.T.</td>
                        <td>: '.$this->formatRut($this->dte['Encabezado']['Receptor']['RUTRecep']).'</td>
                    </tr>
                    <tr>
                        <td class="titulo">DIRECCION</td>
                        <td>: '.$this->dte['Encabezado']['Receptor']['DirRecep'].'</td>
                        <td class="titulo">FECHA EMISION</td>
                        <td>: '.$fecha_emision.'</td>
                    </tr>
                    <tr>
                        <td class="titulo">GIRO</td>
                        <td>: '.$this->dte['Encabezado']['Receptor']['GiroRecep'].'</td>
                        <td class="titulo">FECHA VENCIMIENTO</td>
                        <td>: '.$fecha_emision.'</td>                        
                    </tr>
                    <tr>
                        <td class="titulo">COMUNA</td>
                        <td>: '.$this->dte['Encabezado']['Receptor']['CmnaRecep'].'</td>
                        <td class="titulo">'.$opctexto[1].'</td>
                        <td>: '.$opcvalor[1].'</td>
                    </tr>
                    <tr>
                        <td class="titulo">'.$opctexto[0].'</td>
                        <td>: '.$opcvalor[0].'</td>
                        <td class="titulo"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        ';
        return $html;
    }

    private function setReferencias(){
        $referencias = (isset($this->dte['Referencia'])) ? $this->dte['Referencia'] : [];
        
        if (!empty($referencias)&&!isset($referencias[0]))
            $referencias = [$referencias];        
            
        $html = '
            <div class="espacio-5"></div>
            <table>
                <thead>
                    <tr>
                        <th align="left">TIPO DOCUMENTO</th>
                        <th>FOLIO</th>
                        <th>FECHA</th>
                        <th>RAZON</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($referencias as $ref){
                    $html .= '
                    <tr>
                        <td align="left">'.$this->getTipo( $ref['TpoDocRef'] ).'</td>
                        <td>'.$ref['FolioRef'].'</td>
                        <td>'.date('d-m-Y', strtotime($ref['FchRef'])).'</td>
                        <td>'.$ref['RazonRef'].'</td>
                    </tr>
                    ';
                }
                for($i = 0; $i < 3-count($referencias); $i++){
                    $html .= '
                    <tr>
                        <td align="left">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    ';
                }
                $html .=' 
                </tbody>
            </table>
        ';
        return $html;
    }

    private function setDetalle(){
        $subtotal = 0;
        $detalles = $this->dte['Detalle'];

        $iva = (isset($this->dte['Encabezado']['Totales']['IVA'])) ? $this->dte['Encabezado']['Totales']['IVA'] : 0;
        $neto = (isset($this->dte['Encabezado']['Totales']['MntNeto'])) ? $this->dte['Encabezado']['Totales']['MntNeto'] : 0;
        $descuento = (isset($this->dte['DscRcgGlobal'])) ? $this->dte['DscRcgGlobal']['ValorDR'] : 0;
        $exento = (isset($this->dte['Encabezado']['Totales']['MntExe'])) ? $this->dte['Encabezado']['Totales']['MntExe'] : 0;
        $tasa = (isset($this->dte['Encabezado']['Totales']['TasaIVA'])) ? $this->dte['Encabezado']['Totales']['TasaIVA'] : 19;
        
        $nro_totales = 5;
        $nro_totales += ($descuento != 0) ? 1:0; // Si el descuento != 0 se agrega en el documento, de lo contrario, se quita.
        if (!isset($detalles[0]))
            $detalles = [$detalles];
        $html = '
            <table>
                <thead>
                    <tr>';
                    foreach($this->cols as $key => $value){
                        $html .= '<th width="'.$value['width'].'%">'.$key.'</th>';
                    }
        $html .= '
            </tr>
                </thead>
                    <tbody>';
                if($detalles == null)
                    return '';
                    
                    foreach($detalles as $detalle){
                        $subtotal += intval($detalle['MontoItem']);
                        $und = (!isset($detalle['UnmdItem'])) ? 'Und' : $detalle['UnmdItem'];
                        $cantidad = (!isset($detalle['QtyItem'])) ? 1 : $detalle['QtyItem'];
                        $precio = (!isset($detalle['PrcItem'])) ? 0 : $detalle['PrcItem'];
                        $dscto = (!isset($detalle['DescuentoPct']) ? 0 :  $detalle['DescuentoPct']); 

                        $html.='<tr class="producto">
                        <td width="'.$this->cols['CANTIDAD']['width'].'%" class="numero">'.$cantidad.'</td>
                        <td width="'.$this->cols['UNIDAD']['width'].'%" style="text-align: center">'.$und.'</td>
                        <td width="'.$this->cols['DETALLE']['width'].'%">'.$detalle['NmbItem'].'</td>
                        <td width="'.$this->cols['P. UNITARIO']['width'].'%" class="numero">'.$this->formatNumber($precio).'</td>
                        <td width="'.$this->cols['DSCTO']['width'].'%" style="text-align: center">'.$this->formatNumber($dscto).'%</td>
                        <td width="'.$this->cols['TOTAL']['width'].'%" class="numero">'.$this->formatNumber($detalle['MontoItem']).'</td>
                    </tr>';
                    }

                    for($i = 0; $i < 30-count($detalles); $i++){
                        $html .= '<tr class="producto"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                    }
                    $timbre = '';
                    if($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 0){
                        $timbre = $this->setTimbre().'
                            <p>Timbre Electronico SII</p>
                            '.$this->getResolucion().'
                            <p>Verifique documento: www.sii.cl</p>';
                    }

                    $html.='<tr>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;" colspan="3"></td>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;"></td>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;" colspan="2"></td>
                    </tr>
                    <tr>
                        <td style="border-left: 0; border-bottom: 0; padding-right: 70px; text-align: center;" rowspan="'.$nro_totales.'" colspan="3">
                            '.$timbre.'
                        </td>
                        <td style="padding-top: 5px;" class="total titulo">SUBTOTAL</td>
                        <td class="total valor" colspan="2">$'.$this->formatNumber($subtotal).'</td>
                    </tr>';
                    if($descuento != 0){
                        $html .= '
                            <tr>
                                '.$this->formatValor('DSCTO', $descuento).'
                            </tr>';
                    }
                    $html .= '<tr>
                        <td class="total titulo">EXENTO</td>
                        <td class="total valor" colspan="2">$'.$this->formatNumber($exento).'</td>
                    </tr>';
                    if($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 39){
                        $html .='
                        <tr>
                            <td class="total titulo">NETO</td>
                            <td class="total valor" colspan="2">$'.$this->formatNumber($neto).'</td>
                        </tr>
                        <tr>
                            <td class="total titulo">I.V.A( '.$tasa.'% )</td>
                            <td class="total valor" colspan="2">$'.$this->formatNumber($iva).'</td>
                        </tr>';
                    }
                    $html .= '<tr>
                        <td class="total titulo" style="border-bottom: 1px solid black">TOTAL</td>
                        <td class="total valor"  colspan="2" style="border-bottom: 1px solid black">$'.$this->formatNumber($this->dte['Encabezado']['Totales']['MntTotal']).'</td>
                    </tr>
                </tbody>
            </table>
        ';
        return $html;
    }

    private function formatValor($titulo, $valor){
        $html = '';
        if($valor != 0){
            $html = '
                <td class="total titulo">'.$titulo.'</td>
                <td class="total valor" colspan="2">$'.$this->formatNumber($valor).'</td>
            ';
        }
        return $html;
    }

    private function setAcuseRecibo(){
        $leyenda = ($this->dte['Encabezado']['IdDoc']['TipoDTE']==52) ? 'CEDIBLE CON SU FACTURA' : 'CEDIBLE';
        $html = '
            <table class="acuse-recibo">
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
            <div class="texto-cedible">'.$leyenda.'</div>
        ';
        return $html;
    }

    private function setTimbre(){
        $b2d = new \Milon\Barcode\DNS2D();
        //$b2d->setStorPath(dirname(__FILE__)."/cache/");
        $ted = "CODIGO DE VISTA PREVIA";
        if($this->ted != null){
            $ted = $this->ted;
        }
        $timbre = '<img style="width: 8cm; height: 2.5cm;"src="data:image/png;base64,'.$b2d->getBarcodePNG($ted, "PDF417,,5").'">';
            
        return $timbre;
    }

    private function setInfoPOS($acuse){
        
        $html = $this->setCuadroPOS();
        $html .= $this->setEmisorPOS();
        $html .= '<div class="wrap"><hr></div>';
        $html .= ($this->dte['Encabezado']['Receptor']['RUTRecep']=="66666666-6"&&$this->dte['Encabezado']['IdDoc']['TipoDTE']==39)?'':$this->setReceptorPOS();
        $html .= $this->setReferenciasPOS();
        $html .= $this->setDetallePOS();
        $html .= '<hr>';
        $html .= $this->setTotalPOS();
        $html .= $this->setTimbrePOS();
        if($acuse)
            $html .= $this->setAcuseReciboPOS();
        return $html;
    }

    private function setCuadroPOS(){
        $inferior = ($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 0) ? '<p class="sucursal"><b>S.I.I. - '.\SolucionTotal\CorePDF\SII::getDireccionRegional($this->dte['Encabezado']['Emisor']['CmnaOrigen']).'</b></p>':'';
        $html = '<div class="cuadro">';
        $html .= '<div class="bordes">';
        $html .= '<p><b>R.U.T.: '.$this->formatRut($this->dte['Encabezado']['Emisor']['RUTEmisor']).'</b></p>';
        $html .= '<p><b>'.$this->getTipo($this->dte['Encabezado']['IdDoc']['TipoDTE']).'</b></p>';
        $html .= '<p><b>Nº '.$this->dte['Encabezado']['IdDoc']['Folio'].'</b></p>';
        $html .= '</div>';
        $html .= $inferior;
        $html .= '</div>';

        return $html;
    }

    private function setEmisorPOS(){
        $tipo = $this->dte['Encabezado']['IdDoc']['TipoDTE'];
        $html = '<div class="logo">';
        $html .= '<img src="'.$this->logo.'">';
        $html .= '</div>';
        $html .= '<div class="emisor">';
        $html .= '<p><b>'.$this->dte['Encabezado']['Emisor'][($tipo == 39) ? 'RznSocEmisor':'RznSoc'].'</b></p>';
        $html .= '<p style="padding-top: 0; padding-bottom: 0;word-wrap: break-word; line-height: normal;">'.$this->dte['Encabezado']['Emisor'][($tipo == 39) ? 'GiroEmisor' : 'GiroEmis'].'</p>';
        $html .= '<p>'.$this->dte['Encabezado']['Emisor']['DirOrigen'].', '.$this->dte['Encabezado']['Emisor']['CmnaOrigen'].'</p>';
        $html .= $this->telefono;
        $html .= $this->mail;
        $html .= $this->web;
        $html .= '</div>';

        return $html;
    }

    private function setReferenciasPOS(){
        $html = '';
        $referencias = (isset($this->dte['Referencia'])) ? $this->dte['Referencia'] : [];
        
        if (!empty($referencias)&&!isset($referencias[0]))
            $referencias = [$referencias];  

        if(!empty($referencias)){
            $html = '<table class="tabla2">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th align="left">TIPO DOCUMENTO</th>';
            $html .= '<th>FOLIO</th>';
            $html .= '<th>FECHA</th>';
            $html .= '<th>RAZON</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach($referencias as $ref){
                $html .= '
                <tr>
                    <td align="left">'.$this->getTipo( $ref['TpoDocRef'] ).'</td>
                    <td>'.$ref['FolioRef'].'</td>
                    <td>'.date('d-m-Y', strtotime($ref['FchRef'])).'</td>
                    <td>'.$ref['RazonRef'].'</td>
                </tr>
                ';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    private function setDetallePOS(){

        $detalles = $this->dte['Detalle'];

        if (!isset($detalles[0]))
            $detalles = [$detalles];

        $html = '<table class="tabla">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th width="'.$this->cols['CANTIDAD']['width'].'%">CANT.</th>';
        $html .= '<th width="'.$this->cols['DETALLE']['width'].'%">PRODUCTO</th>';
        $html .= '<th width="'.$this->cols['UNIDAD']['width'].'%">UNIDAD</th>';
        $html .= '<th width="'.$this->cols['P. UNITARIO']['width'].'%">PRECIO</th>';
        $html .= '<th width="'.$this->cols['DSCTO']['width'].'%">DSCTO</th>';
        $html .= '<th width="'.$this->cols['TOTAL']['width'].'%">TOTAL</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach($detalles as $detalle){
            $this->subtotal += intval($detalle['MontoItem']);
            $und = (!isset($detalle['UnmdItem'])) ? 'Und' : $detalle['UnmdItem'];
            $cantidad = (!isset($detalle['QtyItem'])) ? 1 : $detalle['QtyItem'];
            $precio = (!isset($detalle['PrcItem'])) ? 0 : $detalle['PrcItem'];
            $dscto = (!isset($detalle['DescuentoPct']) ? 0 :  $detalle['DescuentoPct']); 

            $html.= '<tr>
                        <td>'.$cantidad.'</td>
                        <td>'.$detalle['NmbItem'].'</td>
                        <td>'.$und.'</td>
                        <td>'.$this->formatNumber($precio).'</td>
                        <td>'.$this->formatNumber($dscto).'%</td>
                        <td>'.$this->formatNumber($detalle['MontoItem']).'</td>
                    </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }

    private function setTotalPOS(){
        $subtotal = 0;
        $detalles = $this->dte['Detalle'];

        $iva = (isset($this->dte['Encabezado']['Totales']['IVA'])) ? $this->dte['Encabezado']['Totales']['IVA'] : 0;
        $neto = (isset($this->dte['Encabezado']['Totales']['MntNeto'])) ? $this->dte['Encabezado']['Totales']['MntNeto'] : 0;
        $descuento = (isset($this->dte['DscRcgGlobal'])) ? $this->dte['DscRcgGlobal']['ValorDR'] : 0;
        $exento = (isset($this->dte['Encabezado']['Totales']['MntExe'])) ? $this->dte['Encabezado']['Totales']['MntExe'] : 0;
        $tasa = (isset($this->dte['Encabezado']['Totales']['TasaIVA'])) ? $this->dte['Encabezado']['Totales']['TasaIVA'] : 19;
        $subtotal = $neto + $exento;
        $total = (isset($this->dte['Encabezado']['Totales']['MntTotal'])) ? $this->dte['Encabezado']['Totales']['MntTotal'] : 0;

        $html = '<table class="total"><thead></thead><tbody>';
        $html .= '<tr>';
        $html .= '<td class="margen-izq" colspan="2"><b>SUBTOTAL:</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($this->subtotal).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="margen-izq" colspan="2"><b>DESCUENTO:</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($descuento).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="margen-izq" colspan="2"><b>EXENTO:</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($exento).'</td>';
        $html .= '</tr>';
        if($this->dte['Encabezado']['IdDoc']['TipoDTE'] != 39){
            $html .= '<tr>';
            $html .= '<td class="margen-izq" colspan="2"><b>NETO:</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($neto).'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="margen-izq" colspan="2"><b>I.V.A ('.$tasa.'%):</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($iva).'</td>';
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td class="margen-izq" colspan="2"><b>TOTAL:</b></td><td></td><td class="derecha">$</td><td class="izquierda">'.$this->formatNumber($total).'</td>';
        $html .= '</tr>';
        $html .= '</tbody></table>';
        return $html;
    }

    private function setReceptorPOS(){
        $tipo = $this->dte['Encabezado']['IdDoc']['TipoDTE'];
        $textoguia = [
            'Tipo de traslado',
            'Tipo de despacho'
        ];
        $valorguia = [
            (!isset($this->dte['Encabezado']['IdDoc']['IndTraslado']))?'':\SolucionTotal\CorePDF\SII::getTipoTraslado($this->dte['Encabezado']['IdDoc']['IndTraslado']),
            (!isset($this->dte['Encabezado']['IdDoc']['TipoDespacho']))?'':\SolucionTotal\CorePDF\SII::getTipoDespacho($this->dte['Encabezado']['IdDoc']['TipoDespacho'])
        ];
        $textodoc = [
            'Medio de pago',
            'Condición de pago'
        ];
        $valordoc = [
            (!isset($this->dte['Encabezado']['IdDoc']['MedioPago']))?'':$this->dte['Encabezado']['IdDoc']['MedioPago'],
            (!isset($this->dte['Encabezado']['IdDoc']['FmaPago']))?'':\SolucionTotal\CorePDF\SII::getFormaPago($this->dte['Encabezado']['IdDoc']['FmaPago'])
        ]; 
        $opctexto = ($this->dte['Encabezado']['IdDoc']['TipoDTE']!=52) ? $textodoc : $textoguia;
        $opcvalor = ($this->dte['Encabezado']['IdDoc']['TipoDTE']!=52) ? $valordoc : $valorguia;
        $fecha_emision = date('d-m-Y', strtotime($this->dte['Encabezado']['IdDoc']['FchEmis']));
        $html = '<div class="receptor">';
        $html .= '<p><b>'.$this->dte['Encabezado']['Receptor']['RznSocRecep'].'</b></p>';
        $html .= '<p>RUT: '.$this->formatRut($this->dte['Encabezado']['Receptor']['RUTRecep']).'</p>';
        if($tipo != 39){
            $html .= '<p>Giro: '.$this->dte['Encabezado']['Receptor']['GiroRecep'].'</p>';
        }
        $html .= '<p>Direccion: '.$this->dte['Encabezado']['Receptor']['DirRecep'].'</p>';
        $html .= '<p>Comuna: '.$this->dte['Encabezado']['Receptor']['CmnaRecep'].'</p>';
        $html .= '<div class="wrap-min"></div>';
        $html .= '<p><b>'.$opctexto[0].':</b> '.$opcvalor[0].'</p>';
        $html .= '<p><b>'.$opctexto[1].':</b> '.$opcvalor[1].'</p>';
        $html .= '<p><b>Fecha emisión:</b> '.$fecha_emision.'</p>';
        $html .= '</div>';
        return $html;
    }

    private function setAcuseReciboPOS(){
        $leyenda = ($this->dte['Encabezado']['IdDoc']['TipoDTE']==52) ? 'CEDIBLE CON SU FACTURA' : 'CEDIBLE';
        $html = '
            <table>
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
            <div class="texto-cedible">'.$leyenda.'</div>
        ';
        return $html;
    }
    
    private function setTimbrePOS(){
        $b2d = new \Milon\Barcode\DNS2D();
        //$b2d->setStorPath(dirname(__FILE__)."/cache/");
        $ted = "CODIGO DE VISTA PREVIA";
        if($this->ted != null){
            $ted = $this->ted;
        }
            
        $html = '<div class="codigo">';
        $html .= '<img style="width: 8cm; height: 2.5cm;"src="data:image/png;base64,'.$b2d->getBarcodePNG($ted, "PDF417,,5").'">';
        $html .= '<p>Timbre electrónico SII</p>';
        $html .= '<p>'.$this->getResolucion().'</p>';
        $html .= '<p>Verifique documento en dte.soluciontotal.cl</p>';
        $html .= '</div>';
        return $html;
    }
}