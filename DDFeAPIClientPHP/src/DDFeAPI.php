<?php

class DDFeAPI{

    private $token;
    
    public function __construct() {
        $this->token = 'COLOQUE_TOKEN';
    }

    // Esta função envia um conteúdo para uma URL, em requisições do tipo POST
    private function enviaConteudoParaAPI($conteudoAEnviar, $url, $tpConteudo){

        //Inicializa cURL para uma URL->
        $ch = curl_init($url);
        
        //Marca que vai enviar por POST(1=SIM)->
        curl_setopt($ch, CURLOPT_POST, 1);
        
        //Passa um json para o campo de envio POST->
        curl_setopt($ch, CURLOPT_POSTFIELDS, $conteudoAEnviar);
        
        //Marca como tipo de arquivo enviado json
        if ($tpConteudo == 'json')
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-AUTH-TOKEN: ' . $this->token));
        else if ($tpConteudo == 'xml')
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'X-AUTH-TOKEN: ' . $this->token));
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain', 'X-AUTH-TOKEN: ' . $this->token));
        
        //Marca que vai receber string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //Inicia a conexão
        $result = curl_exec($ch);
        
        if (curl_error($ch)) {
            echo 'Erro na comunicacao: ' . '<br>';
            echo '<br>';
            echo '<pre>';
            var_dump(curl_getinfo($ch));
            echo '</pre>';
            echo '<br>';
            var_dump(curl_error($ch));
        }

        //Fecha a conexão
        curl_close($ch);

        return json_decode($result, true);
    }
    
    // Para manifestar um documento emitido contra seu cliente
    public function manifestacao($CNPJInteressado, $tpEvento, $nsu, $xJust = '', $chave = ''){
        $json = '';

        if ($nsu != '') {

            $json = '{"CNPJInteressado"": "' . $CNPJInteressado . '", "nsu": "' . $nsu . '",';
        
        }else {

            $json = '{"CNPJInteressado"": "' . $CNPJInteressado . '", "chave": "' . $chave . '",';
        }

        $json = $json . '"manifestacao" : {' . '"tpEvento": "' . $tpEvento . '"';

        if($tpEvento = '210240'){
            $json = $json . ', "xJust": "' . $xJust . '"';
        }

        $json = $json . '}}';

        $url = 'https://ddfe.ns.eti.br/events/manif';

        $this->gravaLinhaLog('[MANIFESTACAO_DADOS]');
        $this->gravaLinhaLog($json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, "json");

        $this->gravaLinhaLog('[MANIFESTACAO_RESPOSTA]');
        $this->gravaLinhaLog($resposta);

        $this->tratamentoManifestacao($resposta);

        return $resposta;
    }

    public function tratamentoManifestacao($jsonRetorno) {
        $xMotivo = '';
        $status = $jsonRetorno['status'];

        if ($status == 200){
            $xMotivo = $jsonRetorno['retEvento']['xMotivo'];

        } elseif ($status == -3) {
            $xMotivo = $jsonRetorno['erro']['xMotivo'];
        } else {
            $xMotivo = $jsonRetorno['motivo'];
        }
        
        echo $xMotivo;
        $this->gravaLinhaLog($xMotivo);
    }


    // Para fazer o download de um unico documento
    public function downloadUnico($CNPJInteressado, $caminho, $tpAmb, $nsu, $modelo, $chave, $incluirPdf = 'false', $apenasComXml = 'false', $comEventos = 'false') {
        $json = '{' . '"CNPJInteressado": "' . $CNPJInteressado . '",';
        if($nsu != ''){

            $json = $json . 
                    '"nsu": "' . $nsu . '",' . 
                    '"modelo": "' . $modelo . '",' .
                    '"incluirPDF": ' . $incluirPdf . ',' . 
                    '"tpAmb": "' . $tpAmb . '"' . '}';
        
        } else {
            $json = $json . 
                    '"chave": "' . $chave . '",' . 
                    '"apenasComXml": ' . $apenasComXml . ',' . 
                    '"comEventos": ' . $comEventos . ',' . 
                    '"incluirPDF": ' . $incluirPdf . ',' . 
                    '"tpAmb": "' . $tpAmb . '"' . '}';            
        }

        $url = 'https://ddfe.ns.eti.br/dfe/unique';

        $this->gravaLinhaLog('[DOWNLOAD_UNICO_DADOS]');
        $this->gravaLinhaLog($json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, "json");
        $this->gravaLinhaLog('[DOWNLOAD_UNICO_RESPOSTA]');
        $this->gravaLinhaLog($resposta);

        $this->tratamentoDownloadUnico($caminho, $incluirPdf, $resposta);

        return $resposta;
    }
        
    public function tratamentoDownloadUnico($caminho, $incluirPdf, $jsonRetorno) {
        
        $status = $jsonRetorno['status'];

        if($status == 200) {
            $this->downloadDocUnico($caminho, $incluirPdf, $jsonRetorno);
            echo 'Download Uncio feito com sucesso';
        } else {
            echo $jsonRetorno['motivo'];
        }

    }

    public function downloadDocUnico($caminho, $incluirPdf, $jsonRetorno) {
        
        $listaDocs = $jsonRetorno['listaDocs'];

        if (substr($caminho, -1) != '\\') $caminho = $caminho . '\\';

        if ($listaDocs == false) {
            $xml = $jsonRetorno['xml'];
            $chave = $jsonRetorno['chave'];
            $modelo = $jsonRetorno['modelo'];
            $this->salvaXML($xml, $caminho, $chave, $modelo);

            if ($incluirPdf == 'true'){
                $pdf = $jsonRetorno['pdf'];
                $this->salvaPDF($pdf, $caminho, $chave, $modelo); 
            }
        } else {
            $arrayXMLS = $jsonRetorno['xmls'];
            
            foreach ($arrayXMLS as $docXML) {

                $xml = $docXML['xml'];

                if ($xml!= '' || $xml != null) {

                    $chave = $docXML['chave'];
                    $modelo = $docXML['modelo'];
                    $tpEvento = $docXML['tpEvento'];
                    if (is_null($tpEvento)) $tpEvento = '';
                    $this->salvaXML($xml, $caminho, $chave, $modelo, $tpEvento);

                    if($incluirPdf == 'true') {
                        $pdf = $docValue['pdf'];
                        $this->salvaPDF($pdf, $caminho, $chave, $modelo, $tpEvento);
                    }
                }
            }
        }
    }



    // Para fazer o download de lote de documentos 
    public function downloadLote($CNPJInteressado, $caminho, $tpAmb, $ultNSU, $modelo, $apenasPendManif = 'false', $apenasComXml = 'false', $comEventos = 'false', $incluirPdf = 'false') {
        $json = '';
        if ($apenasPendManif == 'true') {

            $json = '{"CNPJInteressado": "' . $CNPJInteressado . '", ' . 
                    '"ultNSU": ' . $ultNSU . ', ' . 
                    '"modelo": "' . $modelo . '", ' .
                    '"tpAmb": "' . $tpAmb . '", ' . 
                    '"incluirPDF": ' . $incluirPdf . ', ' .
                    '"apenasPendManif": ' . $apenasPendManif . '}';
        } else {

            $json = '{"CNPJInteressado": "' . $CNPJInteressado . '", ' . 
                    '"ultNSU": ' . $ultNSU . ', ' .
                    '"modelo": "' . $modelo . '", ' . 
                    '"tpAmb": "' . $tpAmb . '", ' . 
                    '"incluirPDF": ' . $incluirPdf . ', ' .
                    '"apenasComXml": ' . $apenasComXml . ', ' .
                    '"comEventos": ' . $comEventos . '}';
        }

        $url = 'https://ddfe.ns.eti.br/dfe/bunch';

        $this->gravaLinhaLog('[DOWNLOAD_LOTE_DADOS]');
        $this->gravaLinhaLog($json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, 'json');

        $this->gravaLinhaLog('[DOWNLOAD_LOTE_RESPOSTA]');
        $this->gravaLinhaLog($resposta);

        $this->tratamentoDownloadLote($caminho, $incluirPdf, $resposta);
     
        return $resposta;
    }

    public function tratamentoDownloadLote($caminho, $incluirPdf, $jsonRetorno) {
        
        $status = $jsonRetorno['status'];
        if ($status == 200) {
            echo 'utilmo NSU: ' . $this->downloadDocsLote($caminho, $incluirPdf, $jsonRetorno);
        } else {
            echo $jsonRetorno['motivo'];
        }
    }


    public function downloadDocsLote($caminho, $incluirPdf, $jsonRetorno) {
        
        if (substr($caminho, -1) != '\\') $caminho = $caminho . '\\';
        
        $arrayXMLS = $jsonRetorno['xmls'];
        foreach ($arrayXMLS as $docXML) {

            $xml = $docXML['xml'];

            if ($xml != '' || $xml != null) {

                $chave = $docXML['chave'];
                $modelo = $docXML['modelo'];
                $tpEvento = $docXML['tpEvento'];
                if (is_null($tpEvento)) $tpEvento = '';

                $this->salvaXML($xml, $caminho, $chave, $modelo, $tpEvento);

                if($incluirPdf == 'true') {
                    $pdf = $docXML['pdf'];
                    $this->salvaPDF($pdf, $caminho, $chave, $modelo, $tpEvento);
                }                    
            }
        }

        return $jsonRetorno['ultNSU'];
    }


    // Utilitários
    public function gravaLinhaLog($msg){
        $dir = './log/';
        if(!file_exists($dir)) mkdir($dir);
        $arq = fopen($dir.date('Ymd').'.log', 'a+');
        $msg = sprintf("[%s]: %s%s", date('Y/m/d H:i:s'), print_r($msg, TRUE), PHP_EOL);
        fwrite($arq, $msg);
        fclose($arq);
    }   
    
    public function salvaPDF($pdf, $caminho, $chave, $modelo, $tpEvento = ''){

        if ($modelo == 55) {
            $extencao = '-procNFe.pdf';
        } elseif ($modelo == 57) {
            $extencao = '-procCTe.pdf';
        } else {
            $extencao = '-procNFSeSP.pdf';
        }

        if(!file_exists($caminho . 'pdfs\\')) mkdir($caminho . 'pdfs\\');

        $localSalvar = $caminho . 'pdfs\\' . $tpEvento . $chave . $extencao;
        $fp = fopen($localSalvar, 'w+');
        fwrite($fp, base64_decode($pdf));
        fclose($fp);
    }
    
    public function salvaXML($xml, $caminho, $chave, $modelo, $tpEvento = ''){
        if ($modelo == 55) {
            $extencao = '-procNFe.xml';
        } elseif ($modelo == 57) {
            $extencao = '-procCTe.xml';
        } else {
            $extencao = '-procNFSeSP.xml';
        }
    
        if(!file_exists($caminho . 'xmls\\')) mkdir($caminho . 'xmls\\');

        $localSalvar = $caminho . 'xmls\\' .  $tpEvento . $chave . $extencao;
        $fp = fopen($localSalvar, 'w+');
        fwrite($fp, $xml);
        fclose($fp);
    }

}
?>