# Infinity Mobile

## Sobre o projeto
E-commerce de iPhones desenvolvido como atividade do 4º bimestre do curso técnico em Tecnologia da Informação do Senac.

## Tecnologias utilizadas
- HTML
- CSS
- PHP
- MySQL
- Bootstrap
- XAMPP
- JavaScript

## Requisitos atendidos
- [x] Uso de HTML, CSS, Bootstrap e JavaScript.
- [x] Conexão com o banco de dados usando PHP e PDO.
- [x] Cadastro e login de usuários.
- [x] Proteção das senhas.
- [x] Exibição dos produtos com imagens.
- [x] Busca e filtro por categoria.
- [x] Página com detalhes dos produtos.
- [x] Carrinho de compras e controle de estoque.
- [x] Finalização da compra com opções de pagamento.
- [x] Painel administrativo.
- [x] Cadastro, edição e exclusão de produtos.
- [x] Visualização dos pedidos.
- [x] Arquivos schema.sql, .env.example e .gitignore.
- [x] Uso do Git e GitHub.

## Funcionalidades
- Vitrine de produtos
- Pesquisa e filtro por categoria
- Detalhes dos produtos
- Cadastro e login de clientes
- Carrinho de compras
- Finalização de pedidos
- Controle de estoque
- Painel administrativo
- Gerenciamento de produtos e pedidos

## Como executar
1. Coloque a pasta do projeto dentro de htdocs do XAMPP.
2. Inicie o Apache e o MySQL.
3. Importe o arquivo database/schema.sql pelo phpMyAdmin.
4. Copie o arquivo .env.example e renomeie a cópia para .env.
5. Configure os dados de conexão com o banco.
6. Acesse http://localhost/infinity-mobile/

## Pagamento
As formas de pagamento são simuladas para fins educacionais.

## Projeto acadêmico
Senac Nações Unidas - 2026.

## Acesso administrativo

1. Realize um cadastro normalmente pelo site.
2. Acesse o phpMyAdmin.
3. Abra o banco infinity_mobile.
4. Acesse a tabela usuarios.
5. Altere o campo tipo do usuário de cliente para admin.
6. Saia do site e faça login novamente.
7. Acesse o painel administrativo.