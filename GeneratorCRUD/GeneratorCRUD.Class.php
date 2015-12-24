<?php
/**
 * Descrição para GeneratorCRUD
 *
 * @author Thiago Teodoro Rodriges
 */
class GeneratorCRUD {
    
    /**
     * Método construtor da GeneratorCRUD
     * 
     * O método construtor já faz a chamada à geração dos ArquivosCRUD para 
     * facilitar o processo. 
     * Ele obtem do banco de dados as tabelas, armazena em um Array para enviar
     * para o método que vai efetivamente criar os arquivos CRUD.
     * 
     * @param String $CaminhoDestinoCRUD Caminho de destino dos arquivos CRUD
     */
    public function __construct($CaminhoDestinoCRUD) {
        
        $Conexao = new Conexao();
                            
        $Sql = 'SHOW TABLES'; //Comando que informa quais tabelas existem no Banco de Dados apontado
            
        $Consulta = $Conexao->AbrirConexao()->prepare($Sql);
            
        $StatusExecucaoConsulta = $Consulta->execute();
        
        if($StatusExecucaoConsulta == TRUE) {
            
            $ArrayTabelasBanco;
            
            while ($Tabelas = $Consulta->fetch(PDO::FETCH_ASSOC)){
                
                $ArrayTabelasBanco[] = $Tabelas['Tables_in_' . BANCODEDADOS];
                
            }
                        
            $TratamentoMensagens = new TratamentoMensagens();
            $TratamentoMensagens->MsgSucesso('Identificação das Tabelas do Banco de Dados : ' . BANCODEDADOS . ' realizada com sucesso!', NULL, NULL);            
            
            //Gerando os Arquivos CRUD -> Enviando o Caminho de Destino dos arquivos e as Tabelas do Banco como parâmetro.
            $this->GeraCRUD($CaminhoDestinoCRUD, $ArrayTabelasBanco);
                        
        } else {
            
            $TratamentoMensagens = new TratamentoMensagens();
            $TratamentoMensagens->MsgErro('Houve um erro ao tentar identificar as Tabelas do Banco de Dados :  ' . BANCODEDADOS, NULL, NULL);            
            
        }                                        
        
    }
    
    /**
     * Método que lê as estruturas das tabelas do banco de dados, e monta um
     * arquivo de classe do Tipo CRUD com as operações básicas de banco de dados
     * 
     * As operações geradas aqui são :
     * 
     * Insert;
     * Update;
     * ConsultaUnica {Por Chave Primaria};
     * ConsultaGeral {Array com Objetos DAO da Tabela};
     * Delete {Por Chave Primaria}; 
     * 
     * @param String $CaminhoDestino Descrição : Caminho de Destino dos Arquivos CRUD
     * @param /Array $ArrayTabelasBanco Descrição : Array com as Tabelas do Banco de Dados
     */
    public function GeraCRUD($CaminhoDestino, $ArrayTabelasBanco) {
     
        for($iArrayTabelas = 0; $iArrayTabelas < count($ArrayTabelasBanco); $iArrayTabelas ++) {
            
            $Conexao = new Conexao();
            
            //Comando que obtem a estrutura da Tabela no Banco de Dados apontado. DESC Nome_Tabela;
            $Sql = 'DESC ' . $ArrayTabelasBanco[$iArrayTabelas];
            
            $Consulta = $Conexao->AbrirConexao()->prepare($Sql);
            
            $StatusExecucaoConsulta = $Consulta->execute();
            
            if($StatusExecucaoConsulta == TRUE) {
                
                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgSucesso('Estrutura da tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . ' obtida com sucesso!', NULL, NULL);
                
                //Verificando a existencia do arquivo NomeDaTabelaCRUD.Class.php na pasta de destino apontada.
                if(file_exists($CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php')) {
                    
                    $TratamentoMensagens = new TratamentoMensagens();
                    $TratamentoMensagens->MsgAlerta('O arquivo : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php já existe na pasta, efetuando exclusão do arquivo CRUD, para geração de um novo Arquivo CRUD atualizado.' , NULL, NULL);
                    
                    $StatusExclusaoArquivo = unlink($CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php');
                    
                    if($StatusExclusaoArquivo == TRUE) {
                        
                        $TratamentoMensagens = new TratamentoMensagens();
                        $TratamentoMensagens->MsgInformacao('Arquivo : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php excluido com sucesso. Criando novo arquivo CRUD.', NULL, NULL);
                        
                        $ArquivoCRUD = fopen($CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php', 'w+');
                        
                        if($ArquivoCRUD == FALSE) {
                            
                            $TratamentoMensagens = new TratamentoMensagens();
                            $TratamentoMensagens->MsgErro('Houve um erro na criação do arquivo : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php, O arquivo não foi criado em : ' . $CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php', NULL, NULL);
                            
                        }
                        
                    } else {
                        
                        $TratamentoMensagens = new TratamentoMensagens();
                        $TratamentoMensagens->MsgErro('Houve um erro ao excluir o arquivo : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php, isso poderá afetar o resultado final da classe.', NULL, NULL);
                        
                    }
                                        
                } else {
                    
                    $ArquivoCRUD = fopen($CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php', 'w+');
                        
                    if($ArquivoCRUD == FALSE) {
                            
                        $TratamentoMensagens = new TratamentoMensagens();
                        $TratamentoMensagens->MsgErro('Houve um erro na criação do arquivo : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php, O arquivo não foi criado em : ' . $CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php', NULL, NULL);
                          
                    }
                    
                }
                
                $Tab = '    '; //Espaçamento de um TAB.
                
                /*
                 * ATENÇÃO PARA MELHOR COMPATIBILIDADE COM AS IDE'S NÃO USAMOS 
                 * ACENTUAÇÃO NAS EXPREÇÕES MESMO NOS COMENTÁRIOS.
                 * NÓS NÃO COLOCAMOS ACENTO EM NADA!
                 */
                
                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgSucesso('Arquivo CRUD : ' . $CaminhoDestino . '/' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php' . ' Criado com Sucesso, Gerando estrutura CRUD...', NULL, NULL);
                                                           
                
                $Escrevendo = fwrite($ArquivoCRUD, '<?php' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, '/**' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * Descricao para ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * Classe do Tipo CRUD com as principais operacoes de Banco de Dados para a Tabela ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * Operacoes Disponiveis : ' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ' . PHP_EOL);                    
                $Escrevendo = fwrite($ArquivoCRUD, ' * Insert;' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * Update;' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ConsultaUnica {Por Chave Primaria};' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ConsultaGeral {Array Com Objetos DAO da Tabela};' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * Delete {Por Chave Primaria};' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * ' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' * @author GeneratorADO_PHP By: Thiago Teodoro Rodrigues' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, ' */ ' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, 'class ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD {' . PHP_EOL);                                        
                $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                
                //Gerando Insert                             
                 
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '/**' . PHP_EOL);                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Metodo que realiza o Insert na tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Caso o Insert aconteca de maneira adequada eh retornado TRUE, caso ocorra algum problema eh retornado FALSE.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Quando for executar insert, nao preencha a chave primaria do objeto, ou se for preencher tenha a certeza de enviar uma chave ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * primaria que nao exista no banco de dados, se voce nao preencher a chave primaria ele vai executar o AUTO_INCREMENT do banco ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * de dados inserindo o dado enviado na ultima posicao. MAS ISSO SOH VAI ACONTECER se a tabela do banco de dados tiver um Chave ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Primaria de AUTO_INCREMENT' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @param \\' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO $' .$ArrayTabelasBanco[$iArrayTabelas] . 'DAO Descricao : Dados a serem inseridos na Tabela por objeto do tipo '  .$ArrayTabelasBanco[$iArrayTabelas] . 'DAO.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @return boolean Descricao : Informa se a operacao foi bem sucedida ou nao.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' */ ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . 'public function Insert'. $ArrayTabelasBanco[$iArrayTabelas] . '($' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);       

                    $StringCamposTabelaInsert = '';

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {

                        if($StringCamposTabelaInsert == '') {

                            $StringCamposTabelaInsert = $Fields['Field'];

                        } else {                                            

                            $StringCamposTabelaInsert = $StringCamposTabelaInsert . ', ' . $Fields['Field'];

                        }
                    }     

                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                    $StringParametrosCamposTabelaInsert = '';

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {

                        if($StringParametrosCamposTabelaInsert == '') {

                            $StringParametrosCamposTabelaInsert = ':' . $Fields['Field'];

                        } else {

                            $StringParametrosCamposTabelaInsert = $StringParametrosCamposTabelaInsert . ', :' . $Fields['Field'];

                        }    
                    }

                    // A barra (/) dentro da string permit escrever a aspa simples sem precisar concatenar ela sinaliza que é para escrever a aspa
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Sql = \'INSERT INTO ' . $ArrayTabelasBanco[$iArrayTabelas] . '(' . $StringCamposTabelaInsert . ') VALUES (' . $StringParametrosCamposTabelaInsert . ');\';' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Conexao = new Conexao();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Insert = $Conexao->AbrirConexao()->prepare($Sql);' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);

                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {

                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Insert->bindValue(\':' . $Fields['Field'] . '\', $' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO->get' . $Fields['Field'] . '());' . PHP_EOL);

                    }

                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$StatusExecucaoInsert = $Insert->execute();' . PHP_EOL);

                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);                
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . 'if($StatusExecucaoInsert == TRUE) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab .$Tab . 'return TRUE;' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '} else {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab .$Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab .$Tab . '$TratamentoMensagens->MsgErro(\'Erro no processamento do Insert no Banco de Dados.\', NULL, NULL);' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab .$Tab . 'return FALSE;' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '}' . PHP_EOL);

                    $Escrevendo = fwrite($ArquivoCRUD, $Tab. '}' . PHP_EOL);                
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                
                //Fim da Geração do Insert
                
                //Gerando Update
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '/**' . PHP_EOL);                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Metodo que realiza o Update na tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Caso o Update aconteca de maneira adequada eh retornado TRUE, caso ocorra algum problema eh retornado FALSE.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @param \\' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO $' . $ArrayTabelasBanco[$iArrayTabelas] .'DAO Descricao : Dados a serem atualizados na Tabela por objeto do tipo '  .$ArrayTabelasBanco[$iArrayTabelas] . 'DAO.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @return boolean Descricao : Informa se a operacao foi bem sucedida ou nao.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' */ ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . 'public function Update'. $ArrayTabelasBanco[$iArrayTabelas] . '($' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);       

                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.
                    
                    $StringCamposJuntoComParametrosUpdate = '';
                    
                    $NumeroDeCamposObtidos = $Consulta->rowCount();
                    
                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                        
                        /*
                         * A primary Key é condição de Update então a String
                         * tem que ser montada com o Where na codição da 
                         * Primary Key no final da Instrução SQL.
                         *  
                         * Então eu identifico a Primary Key e guardo ela 
                         * depois que eu monto todos os campos e parâmetros sem
                         * a primary Key eu vou lá e adiciono a primary key 
                         * como condição do Where. 
                         *
                         * ATENÇÃO, TABELAS SEM PRIMARY KEY NÃO VÃO DAR CERTO
                         * POIS VÃO FICAR SEM CONDIÇÃO DE UPDATE. 
                         * ENTÃO SE CASO EU NÃO IDENTIFICAR UMA PRIMARY KEY
                         * EU SIMPLISMENTE NÃO MONTO O UPDATE DEIXO
                         * UM COMENTÁRIO DIZENDO... //Tabela sem Chave Primaria
                         * impossivel criar Update adequadamente.
                         *  
                         */
                        if($Fields['Key'] == 'PRI') {
                            
                            $CampoPrimaryKey = $Fields['Field'];
                            
                        } else {
                            
                            //Se esse for o último campo a ser obtido, eu não posso colocar a virgula e o espaço no final (, )
                            if($NumeroDeCamposObtidos == 1) {
                                
                                $StringCamposJuntoComParametrosUpdate = $StringCamposJuntoComParametrosUpdate . $Fields['Field'] . ' = :' . $Fields['Field'];
                                
                            } else {
                                
                                //Se não é ultimo campo eu insiro o campo e coloco a virgula e o espaço no final.
                                $StringCamposJuntoComParametrosUpdate = $StringCamposJuntoComParametrosUpdate . $Fields['Field'] . ' = :' . $Fields['Field'] . ', ';
                                
                            }                                                  
                            
                        }
                        
                        //DECREMENTANDO numero de campos para encontrar o ultimo camo e escrever ele de modo diferente.
                        $NumeroDeCamposObtidos --;
                    }
                    
                    //SE o Campo Primary Key não existir eu não vou montar UPDATE por que não se da Update sem Where!!!
                    //Checando a existencia da variavel pois se o campo não existir a variavel nem é criada
                    if(isset($CampoPrimaryKey)) {
                        
                        //Gerando a Clausulá WHERE
                        $CampoPrimaryKey = ' WHERE ' . $CampoPrimaryKey . ' = :' .$CampoPrimaryKey; 
                        
                        //Adicionando A CLAUSULÁ WHERE
                        $StringCamposJuntoComParametrosUpdate = $StringCamposJuntoComParametrosUpdate . $CampoPrimaryKey;

                        // A barra (/) dentro da string permit escrever a aspa simples sem precisar concatenar ela sinaliza que é para escrever a aspa
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Sql = \'UPDATE ' . $ArrayTabelasBanco[$iArrayTabelas] . ' SET ' . $StringCamposJuntoComParametrosUpdate . '\';'. PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Conexao = new Conexao();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Update = $Conexao->AbrirConexao()->prepare($Sql);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);

                        $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                        while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {

                            $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Update->bindValue(\':' . $Fields['Field'] . '\', $' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO->get' . $Fields['Field'] . '());' . PHP_EOL);

                        }
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$StatusExecucaoUpdate = $Update->execute();' .  PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . 'if($StatusExecucaoUpdate == TRUE) {'. PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return TRUE;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '} else {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgErro(\'Erro no processamento do Update no Banco de Dados.\', NULL, NULL);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return FALSE;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '}' . PHP_EOL);                        

                                                                      
                    } else {
                    
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '//A Tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . ' nao possui Chave primaria e por tanto nao pode ser feito um processo de Update com seguranca dos dados garantida!' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                        
                    }

                    $Escrevendo = fwrite($ArquivoCRUD, $Tab. '}' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                
                //Fim da Geração do Update
                
                //Gerando Delete
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '/**' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Metodo que realiza o Delete na tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Caso o Delete aconteca de maneira adequada eh retornado TRUE, caso ocorra algum problema eh retornado FALSE.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @param int $Value  Descricao : Valor da Chave primaria do dado a ser deletado.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @return boolean Descricao : Informa se a operacao foi bem sucedida ou nao.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' */ ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . 'public function Delete'. $ArrayTabelasBanco[$iArrayTabelas] . '($Value) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    
                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                    
                        if($Fields['Key'] == 'PRI') {
                            
                            $PrimaryKey =  $Fields['Field'] ;
                            
                        }

                    }
                    
                    //Se a tabela não tiver Primary Key não vou fazer Delete pois não é possivel realizar uma operação de Delete com segurança
                    if(isset($PrimaryKey)) {                                                                        
                        
                        // A barra (/) dentro da string permit escrever a aspa simples sem precisar concatenar ela sinaliza que é para escrever a aspa
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Sql = \'DELETE FROM ' . $ArrayTabelasBanco[$iArrayTabelas] . ' WHERE ' . $PrimaryKey . ' = :' . $PrimaryKey . ';\';' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Conexao = new Conexao();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Delete = $Conexao->AbrirConexao()->prepare($Sql);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Delete->bindValue(\':' . $PrimaryKey . '\', $Value );' . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$StatusExecucaoDelete = $Delete->execute();' .  PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . 'if($StatusExecucaoDelete == TRUE) {'. PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return TRUE;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '} else {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgErro(\'Erro no processamento do Delete no Banco de Dados.\', NULL, NULL);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return FALSE;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '}' . PHP_EOL);                                                
                        
                    } else {
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '//A Tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . ' nao possui Chave primaria e por tanto nao pode ser feito um processo de Delete com seguranca dos dados garantida!' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                        
                    }

                    $Escrevendo = fwrite($ArquivoCRUD, $Tab. '}' . PHP_EOL);  
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                                        
                //Fim da Geração do Delete   
                    
                //Gerando ConsultaGeral
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '/**' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Metodo que realiza a Consulta Geral de dados na tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Caso a Consulta Geral aconteca de maneira adequada eh retornado um Array(' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO) com os dados da tabela, ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * caso ocorra algum problema ou nenhum dado seja encontrado na tabela eh retornado NULL.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @return /Array(' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO) Descricao : Retorna um Array com os dados obtidos da tabela, ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ou retorna NULL caso aconteca algum problema, tambem eh retornado NULL caso nao seja encontrado nenhum dado na tabela.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' */ ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . 'public function ConsultaGeral'. $ArrayTabelasBanco[$iArrayTabelas] . '() {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab .  '$Sql = \'SELECT * FROM ' . $ArrayTabelasBanco[$iArrayTabelas] . ';\';' . PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Conexao = new Conexao();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Consulta = $Conexao->AbrirConexao()->prepare($Sql);' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                                        
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$StatusExecucaoConsulta = $Consulta->execute();' .  PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . 'if($StatusExecucaoConsulta == TRUE) {'. PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'if($Consulta->rowCount() != 0) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$ArrayConsultaGeralTabela' . $ArrayTabelasBanco[$iArrayTabelas] . ' = Array();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'while ($Dados = $Consulta->fetch(PDO::FETCH_ASSOC)) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '//Criando ' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux populando e adicionando ele ao vetor. ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '$' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux = new ' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    
                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                    
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '$' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux->set' . $Fields['Field'] . '($Dados[\'' . $Fields['Field'] . '\']);' . PHP_EOL);

                    }
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '$ArrayConsultaGeralTabela' . $ArrayTabelasBanco[$iArrayTabelas] . '[] = $' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux;' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '//Destruindo $' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux para que possar ser criado no inicio do While novamente [Economia de uso de memória]' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '$' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAOAux = NULL;' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '}' . PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'return $ArrayConsultaGeralTabela' . $ArrayTabelasBanco[$iArrayTabelas] . ';' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '} else {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgAlerta(\' A Tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . ' esta vazia, nao eh possivel realizar a ConsultaGeral.\', NULL, NULL);' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'return NULL;' .  PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '}' . PHP_EOL);
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '} else {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgErro(\'Erro no processamento do Select [ConsultaGeral] no Banco de Dados.\', NULL, NULL);' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return NULL;' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '}' . PHP_EOL);                                        
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '}' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);                    
                    
                //Fim da Geração da ConsultaGeral
                
                //Gerando ConsultaUnica
                    
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '/**' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Metodo que realiza a Consulta Unica por Chave primaria dos dados na tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . '.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * Caso a Consulta Unica aconteca de maneira adequada eh retornado um ' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO com os dados solicitados, ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * caso ocorra algum problema ou o dado correspondente a consulta nao seja encontrado na tabela eh retornado NULL.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ' . PHP_EOL);  
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @param int $Value  Descricao : Valor da Chave primaria do dado a ser consultado.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * @return /' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO Descricao : Retorna um Objeto com o dado obtido da tabela, ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' * ou retorna NULL caso aconteca algum problema, tambem eh retornado NULL caso o dado correspondente a consulta nao seja encontrado.' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . ' */ ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . 'public function ConsultaUnica'. $ArrayTabelasBanco[$iArrayTabelas] . '($Value) {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                    
                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                        
                        if($Fields['Key'] == 'PRI') {
                            
                            $PrimaryKeyConsultaUnica =  $Fields['Field'] ;
                            
                        }

                    }
                    
                    //Se a tabela não tiver Primary Key não vou fazer Consulta Unica pois não é possivel realizar uma operação de Consulta Unica com segurança
                    if(isset($PrimaryKeyConsultaUnica)) { 
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Sql = \'SELECT * FROM ' . $ArrayTabelasBanco[$iArrayTabelas] . ' WHERE ' . $PrimaryKeyConsultaUnica . ' = :' . $PrimaryKeyConsultaUnica . ';\';' . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Conexao = new Conexao();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Consulta = $Conexao->AbrirConexao()->prepare($Sql);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$Consulta->bindValue(\':' . $PrimaryKeyConsultaUnica . '\', $Value );' . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '$StatusExecucaoConsulta = $Consulta->execute();' .  PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . 'if($StatusExecucaoConsulta == TRUE) {'. PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'if ($Consulta->rowCount() == 1) { //A consulta eh por Chave Primaria deve retornar apenas 1 registro ou nenhum.' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$' . $ArrayTabelasBanco[$iArrayTabelas] . 'Retorno = new ' . $ArrayTabelasBanco[$iArrayTabelas] . 'DAO();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'while ($Dados = $Consulta->fetch(PDO::FETCH_ASSOC)) {' . PHP_EOL);                                                                                               
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        
                        $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.

                        while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {

                            $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . $Tab . '$' . $ArrayTabelasBanco[$iArrayTabelas] . 'Retorno->set' . $Fields['Field'] . '($Dados[\'' . $Fields['Field'] . '\']);' . PHP_EOL);

                        }
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '}' . PHP_EOL);                                                                                               
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'return $' . $ArrayTabelasBanco[$iArrayTabelas] . 'Retorno;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '} else {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgErro(\'Nao encontramos nenhum dado com ' . $PrimaryKeyConsultaUnica . ' : \' . $Value , NULL, NULL);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . $Tab . 'return NULL;' . PHP_EOL);                                
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '}' . PHP_EOL);                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '} else {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens = new TratamentoMensagens();' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . '$TratamentoMensagens->MsgErro(\'Erro no processamento do Select [Consulta Unica] no Banco de Dados.\', NULL, NULL);' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . $Tab . 'return NULL;' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '}' . PHP_EOL);                        
                        
                    } else {
                        
                        $Escrevendo = fwrite($ArquivoCRUD, $Tab . $Tab . '//A Tabela : ' . $ArrayTabelasBanco[$iArrayTabelas] . ' nao possui Chave primaria e por tanto nao pode ser feito um processo de Consulta Unica com seguranca dos dados garantida!' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                        
                    }
                                       
                    $Escrevendo = fwrite($ArquivoCRUD, $Tab . '}' . PHP_EOL);
                    
                //Fim da Geração da ConsultaUnica
                
                
                $Escrevendo = fwrite($ArquivoCRUD, '}' . PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, PHP_EOL);
                $Escrevendo = fwrite($ArquivoCRUD, '?>' . PHP_EOL);                
                                                                                        
                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgSucesso('Arquivo CRUD : ' . $ArrayTabelasBanco[$iArrayTabelas] . 'CRUD.Class.php criado com sucesso!', NULL, NULL);
                
                $PrimaryKey = NULL;
                $CampoPrimaryKey = NULL;
                $PrimaryKeyConsultaUnica = NULL;
                
            } else {
                
                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgErro('Erro na obtenção da Estrutura da tabela : ' . $ArrayTabelasBanco[$iArrayTabelas], NULL, NULL);
                
            }

        }
        
    }
}
