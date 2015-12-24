<link href="bootstrap-3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<script src="jquery-2.1.4/jquery-2.1.4.min.js" type="text/javascript"></script>
<script src="bootstrap-3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
<?php
/* 
 * Arquivo que realiza o disparo e processamento da geração dos arquivos DAO 
 * e CRUD do banco de dados apontado.
 */

//Realizando Importações
require_once './ConfigCharsetPaginas/Config.php';
require_once './ConfigBD/Config/Config.php';
require_once './ConfigBD/Conexao/Conexao.php';
require_once './GeneratorDAO/GeneratorDAO.Class.php';
require_once './GeneratorCRUD/GeneratorCRUD.Class.php';
require_once './TratamentoMensagens_php/TratamentoMensagens.Class.php';


//Criando Diretórios

if(file_exists('ResultFiles/' . BANCODEDADOS) == TRUE) {
    
    $TratamentoMensagens = new TratamentoMensagens();
    $TratamentoMensagens->MsgAlerta('Diretório : ResultFiles/' . BANCODEDADOS . ' já existe.', NULL, NULL);
    
} else {

    $StatusCriacaoDiretorio = mkdir('ResultFiles/' . BANCODEDADOS);

    if($StatusCriacaoDiretorio == TRUE) {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgSucesso('Diretório : ResultFiles/' . BANCODEDADOS . ' criado com sucesso!', NULL, NULL);

    } else {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgErro('Erro na criação do diretório : ResultFiles/' . BANCODEDADOS , NULL, NULL);

    }
}

if(file_exists('ResultFiles/' . BANCODEDADOS . '/DAO') == TRUE) {
    
    $TratamentoMensagens = new TratamentoMensagens();
    $TratamentoMensagens->MsgAlerta('Diretório : ResultFiles/' . BANCODEDADOS . '/DAO já existe.', NULL, NULL);
    
} else {
    
    $StatusCriacaoDiretorio = mkdir('ResultFiles/' . BANCODEDADOS . '/DAO');

    if($StatusCriacaoDiretorio == TRUE) {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgSucesso('Diretório : ResultFiles/' . BANCODEDADOS . '/DAO criado com sucesso!', NULL, NULL);

    } else {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgErro('Erro na criação do diretório : ResultFiles/' . BANCODEDADOS . '/DAO' , NULL, NULL);

    }
}

if(file_exists('ResultFiles/' . BANCODEDADOS . '/CRUD') == TRUE) {

    $TratamentoMensagens = new TratamentoMensagens();
    $TratamentoMensagens->MsgAlerta('Diretório : ResultFiles/' . BANCODEDADOS . '/CRUD já existe.', NULL, NULL);    
    
} else {
    
    $StatusCriacaoDiretorio = mkdir('ResultFiles/' . BANCODEDADOS . '/CRUD');

    if($StatusCriacaoDiretorio == TRUE) {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgSucesso('Diretório : ResultFiles/' . BANCODEDADOS . '/CRUD criado com sucesso!', NULL, NULL);

    } else {

        $TratamentoMensagens = new TratamentoMensagens();
        $TratamentoMensagens->MsgErro('Erro na criação do diretório : ResultFiles/' . BANCODEDADOS . '/CRUD' , NULL, NULL);

    }
}


$GeneratorDAO = new GeneratorDAO('ResultFiles/' . BANCODEDADOS . '/DAO');
$GeneratorCRUD = new GeneratorCRUD('ResultFiles/' . BANCODEDADOS . '/CRUD');