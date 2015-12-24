<?php
/**
 * Descrição para GeneratorDAO
 * 
 * Arquivo que gera para cada tabela os Arquivos DAO (Data Access Object) 
 * do Banco de Dados apontado.
 *
 * @author Thiago Teodoro Rodrigues
 */
class GeneratorDAO {
    
    /**
     * Método construtor da classe.
     * 
     * O método construtor já realiza os disparos para a geração dos arquivos 
     * DAO. Ele obtêm dados e os repassa para as funções afim de automatizar
     * o processo. Para isso ele solicita o caminho de destino dos Arquivos 
     * DAO que serão gerados.
     *  
     * @param String $CaminhoDestinoDAO Caminho de destino dos arquivos DAO.
     */
    public function __construct($CaminhoDestinoDAO) {
        
        $Conexao = new Conexao();
        
        $Sql = 'SHOW TABLES'; //Comando que informa quais tabelas existem no Banco de Dados apontado
        
        $Consulta = $Conexao->AbrirConexao()->prepare($Sql);
        
        $StatusConsulta = $Consulta->execute();
        
        if($StatusConsulta == FALSE) {
            
            $TratamentoMensagens = new TratamentoMensagens();
            $TratamentoMensagens->MsgErro('Houve um erro ao tentar identificar as tabelas do banco de dados ' . BANCODEDADOS, NULL, NULL);
            
        } else {
            
            $ArrayTabelas;
            
            while ($Tabelas = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                
                $ArrayTabelas[] = $Tabelas['Tables_in_' . BANCODEDADOS];
                
            }  
            
            $TratamentoMensagens = new TratamentoMensagens();
            $TratamentoMensagens->MsgSucesso('Identificação das Tabelas do Banco de Dados : ' . BANCODEDADOS . ' realizada com sucesso!', NULL, NULL);
            
            $StatusGeracaoDAO = $this->GeradorDAO($CaminhoDestinoDAO, $ArrayTabelas);
            
        }                
        
    }        
 
    /**
     * Método que cria os arquivos DAO das Tabelas do Banco de dados.
     * Esse método lê o vetor que armazenou todas as tabelas do Banco e posição
     * por posição obtem a estrutura da tabela e cria a Classe DAO a partir 
     * dessa leitura. 
     * 
     * @param String $CaminhoDestino Caminho de Destino dos Arquivos DAO gerados.
     * @param /Array $ArrayTabelas Array com as tabelas do banco de dados.
     * @return Boolean TRUE ou FALSE dependendo do sucesso ou falha da operação.
     */
    public function GeradorDAO($CaminhoDestino, $ArrayTabelas){
        
        for($IArrayTabela = 0; $IArrayTabela < count($ArrayTabelas); $IArrayTabela ++) {
            
            $Conexao = new Conexao();
            
            //Comando que obtem a estrutura da Tabela no Banco de Dados apontado. DESC Nome_Tabela;
            $Sql = 'DESC ' . $ArrayTabelas[$IArrayTabela];            
            
            $Consulta = $Conexao->AbrirConexao()->prepare($Sql);
            
            $StatusConsulta = $Consulta->execute();
            
            if($StatusConsulta == FALSE) {
                
                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgErro('Erro na obtenção da Estrutura da tabela : ' . $ArrayTabelas[$IArrayTabela], NULL, NULL);
                
            } else {

                $TratamentoMensagens = new TratamentoMensagens();
                $TratamentoMensagens->MsgSucesso('Estrutura da tabela : ' . $ArrayTabelas[$IArrayTabela] . ' obtida com sucesso!', NULL, NULL);
             
                
                if(file_exists($CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php') == TRUE) {
                    
                    $TratamentoMensagens = new TratamentoMensagens();
                    $TratamentoMensagens->MsgAlerta('O Arquivo : ' . $CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela]. 'DAO.Class.php' . ' já existe na pasta, excluindo arquivo DAO para geração de um novo arquivo DAO atualizado.' , NULL, NULL);
                    
                    $StatusExclusaoArquivo = unlink($CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php');
                    
                    if($StatusExclusaoArquivo == TRUE) {
                        
                        $TratamentoMensagens->MsgInformacao('Arquivo : ' . $CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php' . ' Excluido com sucesso!', NULL, NULL);
                     
                        
                        $ArquivoDAO = fopen($CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php', 'w+');
                        
                        if($ArquivoDAO == FALSE) {
                            
                            $TratamentoMensagens->MsgErro('Não foi possivel criar o arquivo DAO.', NULL, NULL);
                            
                        }
                        
                    } else {
                        
                        $TratamentoMensagens->MsgErro('Não conseguimos excluir o Arquivo : ' . $CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php' . 'Isso pode acarretar erros na geração dos arquivos DAO.', NULL, NULL);
                        
                    }
                    
                } else {
                    
                    $ArquivoDAO = fopen($CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php', 'w+');
                                                                                
                }
                    
                if($ArquivoDAO == FALSE) {
                            
                    $TratamentoMensagens->MsgErro('Não foi possivel criar o arquivo DAO.', NULL, NULL);
                           
                } else {                    
                    
                    $Tab = '    '; //Espaçamento de um TAB.
                    
                    
                    /*
                     * ATENÇÃO PARA MELHOR COMPATIBILIDADE COM AS IDE'S NÃO USAMOS 
                     * ACENTUAÇÃO NAS EXPREÇÕES MESMO NOS COMENTÁRIOS.
                     * NÓS NÃO COLOCAMOS ACENTO EM NADA!
                     */
                    
                    $TratamentoMensagens->MsgSucesso('Arquivo DAO : ' . $CaminhoDestino . '/' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php' . ' Criado com Sucesso, Gerando estrutura DAO...', NULL, NULL);

                    $Escrevendo = fwrite($ArquivoDAO, '<?php' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, '/**' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' * Descricao para ' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' *' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' * Data Access Object para a Tabela : '. $ArrayTabelas[$IArrayTabela] . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' * ' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' * @author GeneratorADO_PHP By: Thiago Teodoro Rodrigues' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, ' */' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, 'class ' . $ArrayTabelas[$IArrayTabela] . 'DAO {' . PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, $Tab . '//Atributos da Classe' . PHP_EOL);                                        
                    
                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                        
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . 'private $' . $Fields['Field'] . ';' . PHP_EOL);
                        
                    }
                    
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);  
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);  
                    
                    $Escrevendo = fwrite($ArquivoDAO, $Tab . '//Metodos de acesso GET|SET' . PHP_EOL); 
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                    
                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.
                    
                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                        
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . '/**' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * Retorna o ' . $Fields['Field'] . ' do Objeto ' . $ArrayTabelas[$IArrayTabela] . 'DAO' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * ' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * @return ' . $Fields['Type'] . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' */' . PHP_EOL);
                                               
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . 'public function get' . $Fields['Field'] . '() {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . $Tab . 'return $this->' . $Fields['Field'] . ';' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . '}' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 

                    }
                    
                    $Consulta->execute(); //Executando a Consulta novamente pois o último FETCH_ASSOC inutiliza a consulta depois de terminado.
                    
                    while ($Fields = $Consulta->fetch(PDO::FETCH_ASSOC)) {
                        
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . '/**' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * "Seta" o(a) ' . $Fields['Field'] . ' do Objeto ' . $ArrayTabelas[$IArrayTabela] . 'DAO' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * ' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' * @param ' . $Fields['Type'] . ' $' . $Fields['Field'] . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' */' . PHP_EOL);
                                                
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . ' public function set' . $Fields['Field'] . '($' . $Fields['Field'] . ') {' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . $Tab . '$this->' . $Fields['Field'] . ' = $' . $Fields['Field'] . ';' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL); 
                        $Escrevendo = fwrite($ArquivoDAO, $Tab . '}' . PHP_EOL);
                        $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);                        
                        
                    }
                    
                    
                    $Escrevendo = fwrite($ArquivoDAO, '}' . PHP_EOL);  
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, PHP_EOL);
                    $Escrevendo = fwrite($ArquivoDAO, '?>' . PHP_EOL); 
                    
                }                    
                
                fclose($ArquivoDAO);
                
                $TratamentoMensagens->MsgSucesso('Arquivo DAO : ' . $ArrayTabelas[$IArrayTabela] . 'DAO.Class.php gerado com Sucesso!', NULL, NULL);                                
                
            }
            
        }
        
    }
    
}