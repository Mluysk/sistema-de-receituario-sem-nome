# Sistema de Receituário (Custos e Precificação)

Projeto em PHP 8 + MySQL + HTML/CSS/JS, sem frameworks pesados e pronto para XAMPP.

## Requisitos
- PHP 8.x
- MySQL 5.7+
- Apache (XAMPP)

## Instalação no XAMPP
1. Copie a pasta do projeto para `htdocs` (ex: `C:\xampp\htdocs\sistema-de-receituario-sem-nome`).
2. Crie o banco e as tabelas executando o arquivo `database.sql` no phpMyAdmin ou via terminal MySQL.
3. Ajuste as credenciais em `app/config.php` caso necessário.
4. Acesse no navegador: `http://localhost/sistema-de-receituario-sem-nome/public` (o sistema detecta automaticamente a subpasta).

## Estrutura de pastas
- `app/` → configuração, controllers, views e utilitários.
- `public/` → ponto de entrada (`index.php`) e assets (CSS/JS).
- `database.sql` → SQL completo para criação do banco e tabelas.

## Funcionalidades
- Dashboard moderno (dark) com menu lateral.
- CRUD de ingredientes com histórico de preços em modal.
- Cadastro de receitas com itens dinâmicos e cálculo automático de custos.
- Perfis de custos com percentuais e valores fixos configuráveis.
- Relatórios com exportação CSV.
- Segurança básica: PDO + validação + CSRF simples.

## Observações
- Valores monetários usam DECIMAL(10,2) e percentuais DECIMAL(5,2) nas tabelas.
- Formatação PT-BR em toda a interface.
