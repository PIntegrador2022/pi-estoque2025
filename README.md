Feedbacks:

- Adicionar submenu de "Contagem" na side bar, listando todos os produtos apenas com o nome, e botões de + e - para adicionar ou remover (em um) a quantidade de forma rápida.
- Melhor interface para dispositivos móveis, como controles de toque (Menu inicialmente fechado, arraster para abrir menu ou fechar)
- Campo de categoria em cada produto, com opção de filtragem e busca
- Demais análise de dados no dashboard, como produtos em baixo estoque e alerta para produtos faltando (com quantidade igual à zero).

Requisitos da Univesp a cumprir:

- Uso de banco de dados ✓
- Script web ✓
- Controle de Versão ✓
- Integração Contínua ✓
- Testes ✓
- Análise de Dados ✓
- Nuvem ✓
- Acessibilidade (Interface mobile) ✓

Adições opcionais:
- Campos de data de criação e alteração em cada produto, para melhor controle. A data de criação indicando o momento exato do cadastro do produto, e alteração a última data em que o produto sofreu alguma edição.


***Sistema de Estoque***
O Sistema de Estoque é uma aplicação web desenvolvida para gerenciar produtos, movimentações (entradas e saídas) e relatórios em um ambiente empresarial. O objetivo principal é fornecer uma ferramenta intuitiva e eficiente para controle de estoque, permitindo que os usuários realizem operações como cadastro de produtos, registro de movimentações e geração de relatórios detalhados.

***Funcionalidades Principais***
Cadastro de Produtos : Inclusão, atualização e exclusão de produtos no sistema.
Movimentações de Estoque : Registro de entradas (reposição) e saídas (consumo) de produtos.
Geração de Relatórios : Visualização de dados em tabelas e gráficos interativos, com filtros por período e tipo de relatório (consumo, reposição, tendências temporais e valor total movimentado).
Controle de Acesso : Autenticação de usuários e níveis de acesso diferenciados.
Requisitos
Para executar este projeto, você precisará dos seguintes componentes instalados no seu ambiente:

PHP >= 7.4
MySQL
Composer (para gerenciar dependências PHP)
Servidor Web (Apache/Nginx ou PHP Built-in Server para desenvolvimento)

***Instalação***
***Passo 1: Clonar o Repositório***
Clone o repositório usando o comando abaixo:
```
git clone https://github.com/PIntegrador2022/PI-Estoque.git
```
***Passo 2: Instalar as Dependências***
Certifique-se de que o Composer está instalado no seu sistema. Em seguida, execute o seguinte comando para instalar as dependências necessárias já dentro da pagina no projeto:
```
composer install
```
Isso criará a pasta vendor/ e instalará todas as bibliotecas listadas no arquivo composer.json.

***Passo 3: Configurar o Banco de Dados***
1. Crie um banco de dados MySQL. Por exemplo:
```CREATE DATABASE estoque;```
2. Importe o esquema SQL contido no arquivo na pasta do projeto (estoque.sql) para seu o banco de dados que vôce a cabou de criar.
3. Se necessário atualize as credenciais de conexão no arquivo db/connect.php. Substitua os valores pelas suas configurações locais:
```
<?php
$host = 'localhost';
$dbname = 'estoque';
$username = 'seu_usuario';
$password = 'sua_senha';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
```
***Passo 4: Iniciar o Servidor Web***
Você pode usar qualquer servidor web compatível com PHP (Apache/Nginx) ou iniciar um servidor embutido do PHP para testes locais.

***Passo 5: Testar a Aplicação***
No banco de dados importado já contém um administrador, para realizar o login utilizar:
```
Login: admin
Senha: admin123
```

***Estrutura do Projeto***

/PI-Estoque
    ├── .gitignore          <-- Arquivo para ignorar pastas/arquivos desnecessários
    ├── composer.json       <-- Arquivo de dependências do Composer
    ├── composer.lock       <-- Registra versões exatas das dependências
    ├── index.php ...       <-- Página index login da aplicação, e demais arquivos .php que compõe o projeto
    ├── db/
    │   └── connect.php     <-- Configuração de conexão com o banco de dados
    ├── css/
    │   └── login.css       <-- Arquivo de estilos CSS
    │   └── style.css       <-- Arquivo de estilos CSS
    ├── js/
    │   └── scripts.js      <-- Scripts JavaScript
    ├── includes/
    │   └── sidebar.php     <-- Componentes reutilizáveis (ex.: sidebar)
    ├── vendor/             <-- Pasta gerada pelo Composer (ignorada no .gitignore)
    └── README.md           <-- Documentação do projeto


***Geração de Relatórios***
O sistema permite gerar relatórios com base nos seguintes tipos:

***Consumo*** : Mostra a quantidade total consumida de cada produto dentro de um intervalo de datas.
***Reposição*** : Exibe a quantidade total repostada de cada produto dentro de um intervalo de datas.
***Tendências Temporais*** : Apresenta a movimentação total por mês (formato MM-YYYY).
***Valor Total Movimentado*** : Calcula o valor monetário total movimentado por produto.

Para gerar um relatório:
1. Preencha os campos de filtro (data inicial, data final e tipo de relatório).
2. Clique no botão Gerar Relatório .
3. Os resultados serão exibidos em uma tabela e, quando aplicável, em um gráfico interativo.

***Tecnologias Utilizadas***
Backend : PHP (PDO para manipulação de banco de dados)
Frontend : HTML5, CSS3, JavaScript (Chart.js para gráficos interativos)
Banco de Dados : MySQL
Ferramentas : Composer para gerenciamento de dependências, Git para controle de versão.

***Representação Gráfica Simplificada Do BD:***
[usuarios]
+-------------------+
| id (PK)           |
| nome              |
| email             |
| senha             |
| nivel_acesso      |
| created_at        |
| updated_at        |
+-------------------+

[categorias]
+-------------------+
| id (PK)           |
| nome              |
| descricao         |
| created_at        |
| updated_at        |
+-------------------+
        |
        | 1:N
        v

[produtos]
+-------------------+
| id (PK)           |
| nome              |
| descricao         |
| preco             |
| quantidade        |
| estoque_minimo    |
| categoria_id (FK) |
| created_at        |
| updated_at        |
+-------------------+
        |
        | 1:N
        v

[movimentacoes]
+-------------------+
| id (PK)           |
| produto_id (FK)   |
| tipo              |
| quantidade        |
| data_movimentacao |
| observacao        |
| created_at        |
+-------------------+