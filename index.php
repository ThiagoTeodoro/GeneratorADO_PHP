<?php
    //Realizando importações
    require_once './ConfigBD/Config/Config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>GeneratorADO_PHP - By: Thiago Teodoro Rodrigues</title>
        <link href="bootstrap-3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>        
        <script src="jquery-2.1.4/jquery-2.1.4.min.js" type="text/javascript"></script>
        <script src="bootstrap-3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
        <script>
            function AbreJanelaForm(Target, Width, Height) {
                window.open('about:blank', Target,'width=' + Width + ',height=' + Height + ',scrollbars=1');
            }                        
        </script>
    </head>
    <body>
        <div class="container">
            <h1>GeneratorADO_PHP</h1>
            <label>By: Thiago Teodoro Rodrigues</label>
            <br/>
            <h3>Dados do Banco de Dados configurado para geração das Classes DAO e CRUD.</h3>
            <table class="table table-bordered" style="width: 500px;">
                <tr>
                    <td class="success"><h5>Host :</h5></td>
                    <td><h5><strong><?php print_r(HOST); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="success"><h5>Porta :</h5></td>
                    <td><h5><strong><?php print_r(PORTA); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="success"><h5>Banco de Dados :</h5></td>
                    <td><h5><strong><?php print_r(BANCODEDADOS); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="success"><h5>Charset do Banco de Dados :</h5></td>
                    <td><h5><strong><?php print_r(CHARSETDOBANCO); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="success"><h5>Usuário :</h5></td>
                    <td><h5><strong><?php print_r(USUARIO); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="success"><h5>Senha :</h5></td>
                    <td><h5><strong><?php print_r(SENHA); ?></strong></h5></td>
                </tr>                
            </table>
            <h3>Localização dos arquivos DAO e CRUD após geração em relação à pasta do projeto</h3>
            <table class="table table-bordered" style="width: 600px;">
                <tr>
                    <td class="info"><h5>Pasta :</h5></td>
                    <td><h5><strong><?php print_r('ResultFiles/' . BANCODEDADOS); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="info"><h5>Localização dos Arquivos DAO :</h5></td>
                    <td><h5><strong><?php print_r('ResultFiles/' . BANCODEDADOS . '/DAO'); ?></strong></h5></td>
                </tr>
                <tr>
                    <td class="info"><h5>Localização dos Arquivos CRUD :</h5></td>
                    <td><h5><strong><?php print_r('ResultFiles/' . BANCODEDADOS . '/CRUD'); ?></strong></h5></td>
                </tr>                                
            </table>
            <form action="ExibeReadme.php" target="Readme" onsubmit='AbreJanelaForm("Readme",800,600)'>
                <input type="submit" class="btn btn-success" value="Arquivo de explicação" title="Arquivo de Explicação"/>
            </form>
            <br>
            <form action="DisparaGeracao.php" target="Geracao" onsubmit='AbreJanelaForm("Geracao",800,400)'>
                <input type="submit" class="btn btn-danger" value="Gerar DAO e CRUD PHP" title="Gerar DAO e CRUD PHP"/>
            </form>
            <h4>'Live long and prosper. :D'</h4>
        </div>
    </body>
</html>
