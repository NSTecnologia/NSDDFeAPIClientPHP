# NSDDFeAPIClientPHP

## Introdução:

Esta documentação apresenta trechos de códigos de uma classe em PHP que foi desenvolvida para consumir as funcionalidades da NS DDF-e API. Você pode realizar o download da classe e de uma aplicação de exemplo clicando na imagem abaixo:

-----

## Integrando ao sistema:

Para utilizar as funções de comunicação com a API, você precisa realizar os seguintes passos:

1. Extraia o conteúdo da pasta compactada que você baixou;
2. Copie para a pasta da sua aplicação a classe DDFeAPI.php, que esta na pasta raiz;
3. Abra o seu projeto e importe a pasta copiada.

**Pronto!** Agora, você já pode consumir a NS DDF-e API através do seu sistema. Todas as funcionalidades de comunicação foram implementadas na classe **DDFeAPI.php**.

-----

## Realizando uma Manifestação:

Para realizar uma manifestação de um documento emitido contra o CNPJ do seu cliente ou seu, você poderá utilizar a função manifestacao da classe DDFeAPI. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

#### Parâmetros:

ATENÇÃO: o **token** também é um parâmetro necessário, e você deve primeiramente defini-lo na classe **DDFeAPI.php**. Verifique os parâmetros da classe.

Parametros     | Descrição
:-------------:|:-----------
$CNPJInteressado | Conteúdo de emissão do documento.
$tpEvento        | Tipo de evento posto na manifestação:<ul> <li>**210200** – Confirmação da Operação</li> <li>**210240** – Operação não $Realizada</li> <li>**210220** – Desconhecimento da Operação</li> <li>**210210** – Ciência da Operação</li> </ul>
$nsu             | Número Sequencial Único de um DF-e determinado
$xJust           | Justificativa da manifestação (Informar somente quando o tpEvento for 210240)
$chave           | Chave do DF-e que deseja-se manifestar


#### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:
    
    $DDFeAPI = new DDFeAPI;

    //Por nsu
    $retorno = $DDFeAPI->manifestacao('11111111111111', '210200', '134');

    //Por chave
    $retorno = $DDFeAPI->manifestacao('11111111111111', '210200', '', 'TESTE INTEGRAÇÃO NS TECNOLOGIA', '35160324110220000136550010000000351895912462');

    var_dump($retorno);


A função **manifestacao** fará o envio da confirmação de participação do destinatário na operação acobertada pela Nota Fiscal Eletrônica, emitida para o seu CNPJ, para API.

-----

## Realizando um Download Único:

Para realizar um download de um unico documento, você poderá utilizar a função downloadUnico da classe DDFeAPI. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

#### Parâmetros:

ATENÇÃO: o **token** também é um parâmetro necessário, e você deve primeiramente defini-lo na classe **DDFeAPI.php**. Verifique os parâmetros da classe.

Parametros      | Descrição
:-------------: |:-----------
$CNPJInteressado | Conteúdo de emissão do documento.
$caminho         | Local onde serão salvos os documentos
$tpAmb           | Tipo de ambiente evento posto na manifestação:<ul><li>1 - Produção</li><li>2 - Homologação</li></ul>
$nsu             | Número Sequencial Único do DF-e que deseja-se fazer o downloadmodelo
$modelo          | Modelo do documento:<ul> <li>55 - NF-e</li> <li>98 - NFSe SP</li> <li>57 - CT-e</li> </ul>
$chave           | Chave do DF-e que deseja-se manifestar
$incluirPdf      | Incluir do documento auxiliar
$apenasComXml    | Carregar apenas documentos com XMLs disponíveis
$comEventos      | Incluir eventos vinculados ao documento disponíveis

#### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:

    $DDFeAPI = new DDFeAPI;

    //Por nsu
    $retorno = $DDFeAPI->downloadUnico('11111111111111', '.\\Notas\\', '2', '134', '55', '', 'true');

    //Por chave
    $retorno = $DDFeAPI->downloadUnico('11111111111111', '.\\Notas\\', '2', '', '55', '35160324110220000136550010000000351895912462', 'false', 'false', 'true');

    var_dump($retorno);

A função **downloadUnico** fará o envio de um json para API fazendo com que o documento especifico na requisição seja baixado e salvo na maquina.

-----

## Realizando um Donwload em Lote:

Para realizar um download de lote de documentos, você poderá utilizar a função downloadLote da classe DDFeAPI. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

#### Parâmetros:

ATENÇÃO: o **token** também é um parâmetro necessário, e você deve primeiramente defini-lo na classe **DDFeAPI.php**. Verifique os parâmetros da classe.

Parametros      | Descrição
:-------------: |:-----------
$CNPJInteressado | Conteúdo de emissão do documento.
$caminho         | Local onde serão salvos os documentos
$tpAmb           | Tipo de ambiente evento posto na manifestação:<ul><li>1 - Produção</li><li>2 - Homologação</li></ul>
$ultNSU          | Ultimo Número Sequencial Único para fazer download a partir do mesmo
$dhInicial       | Data e hora com fuso horário do período de emissão inicial a ser consultado
$dhFInal         | Data e hora com fuso horário do período de emissão final a ser consultado
$modelo          | Modelo do documento:<ul> <li>55 - NF-e</li> <li>98 - NFSe SP</li> <li>57 - CT-e</li> </ul>
$apenasPendManif | Carregar apenas documentos pendentes de manifestação
$incluirPdf      | Incluir do documento auxiliar
$apenasComXml    | Carregar apenas documentos com XMLs disponíveis
$comEventos      | Incluir eventos vinculados ao documento disponíveis

#### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:
 

    $DDFeAPI = new DDFeAPI;

    $retorno = $DDFeAPI->downloadLote('11111111111111', '\\Notas\\', '2', '0', '55', 'false', 'false', 'true', 'true');
    
    //Por dhIncial e dhFinal
    
    var_dump($retorno);
    
A função **downloadLote** fará o envio de um json para API fazendo com que os documentos, a partir do ultimo NSU, sejam baixados e salvos na maquina.

-----

![Ns](https://nstecnologia.com.br/blog/wp-content/uploads/2018/11/ns%C2%B4tecnologia.png) | Obrigado pela atenção!
