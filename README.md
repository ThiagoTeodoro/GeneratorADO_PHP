# GeneratorADO_PHP

##Versão 1.0 24/12/2015

Projeto para automatização da construção de arquivos básicos do tipo DAO e CRUD para acesso a dados de um banco de dados, a fim de agilizar o processo de produção de projetos em PHP.

Esse projeto foi criado para somar produtividade na hora de gerar os acessos a dados de uma estrutura de banco de dados.
O GeneratorADO_PHP vai ler o banco de dados e gerar para cada tabela do banco que foi apontado um arquivo DAO e arquivo CRUD correspondente à estrutura da tabela. 
O Arquivo DAO terá todos os campos da tabela do banco de dados definidos como atributos com seus métodos de acesso GET e SET correspondentes e o arquivo CRUD terá as operações básicas de banco de dados Insert, Update, ConsultaUnica {Por Chave Primária}, ConsultaGeral {Array com Objetos DAO da Tabela} e Delete {Por Chave Primária}.

Os arquivos gerados são apenas o início de uma estrutura que pode ser melhorada, então nada impede de o programador alterar os arquivos resultantes a seu gosto, a intenção aqui é iniciar uma base para que o programador já tenha os métodos básicos para trabalhar.

##Versão 1.1 29/12/2015

Adicionada no tratamento de erro de execução das SQL's dos arquivos CRUD, a exibição da descrição do erro ocorrido no banco de dados durante a execução da SQL.
Essa implementação vai contribuir para uma melhor interpretação dos erros que ocorrem no banco de dados, caso eles ocorram.


###Orientações
*Nas tabelas de banco de dados os nomes das tabelas não devem conter espaço ou acentuação. 
*Tabelas que não possuem CHAVE PRIMÁRIA ficaram sem as operações de Update, ConsultaUnica, Delete. Pois, sem CHAVE PRIMÁRIA é impossível realizar essas operações com segurança dos dados do Banco de Dados.
*Se uma determinada tabela tiver mais de uma CHAVE PRIMÁRIA será considerada a ultima CHAVE PRIMÁRIA na ordem de criação como condição para  Update, ConsultaUnica, Delete.
*Se uma determinada tabela não tiver CHAVE PRIMÁRIA mais tiver um campo UNIQUEKEY esse campo será considerado para montar os processos de Update, ConsultaUnica, Delete. 

###OBSERVAÇÃO
*Desenvolvido e testado apenas para MYSQL ou MARIADB.