<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>GeneratorADO_PHP - By: Thiago Teodoro Rodrigues</title>
    </head>
    <body>
        <?php
            $Arquivo = fopen('README.md', 'r+');
            
            while (!feof($Arquivo)){
                
                print_r(fgets($Arquivo));
                print_r('<br/>');
                
            }
            
            fclose($Arquivo);        
        ?>
    </body>
</html>
