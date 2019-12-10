<?php
include('./src/DDFeAPI.php');

$DDFeAPI = new DDFeAPI();

/*
	Aqui voce pode testar a chamada de metodos para manifestar,
	fazer o download de um unico documento ou/e fazer download de
	varios documentos emitidos contra co CNPJ do cliente

	- Aqui um exemplo de chamada de download de um unico documento atraves da chave
	  (pode ser feito tanto pela chave do documento ou pelo NSU do mesmo):

		* $DDFeAPI->downloadUnico($CNPJdoInteressado, $caminhoSalvarDoc, $tpAmb, $nsuDoc, $modeloDoc, $chaveDoc, $incluirPdf, $apenasComXml, $comEventos);


	- Aqui um exemplo de chamada de download de lote de documentos 
	  (somente pode ser feito pelo ultimo NSU):
	
		* $DDFeAPI->downloadLote($CNPJdoInteressado, $caminhoSalvarDoc, $tpAmb, $UltNSU, $modelo, $apenasPendManif, $apenasComXml, $comEventos, $incluirPdf);

	Para maiores informações, consulte a documentação no link: https://confluence.ns.eti.br/display/PUB/PHP+-+DDF-e+API, e entre em contato com a equipe de integração

*/


?>
