CREATE DATABASE IF NOT EXISTS receituario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE receituario;

CREATE TABLE ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_ingrediente VARCHAR(150) NOT NULL,
    fornecedor VARCHAR(150) NULL,
    unidade_padrao VARCHAR(10) NOT NULL,
    peso_padrao_g DECIMAL(10,2) NULL,
    preco_por_kg DECIMAL(10,2) NOT NULL,
    observacoes TEXT NULL,
    data_cadastro DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE unidades_padrao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_unidade VARCHAR(20) NOT NULL,
    descricao VARCHAR(255) NULL,
    data_cadastro DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE ingrediente_precos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingrediente_id INT NOT NULL,
    preco_por_kg DECIMAL(10,2) NOT NULL,
    fornecedor VARCHAR(150) NULL,
    observacao VARCHAR(255) NULL,
    data_registro DATETIME NOT NULL,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE receitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_receita VARCHAR(150) NOT NULL,
    rendimento_padrao DECIMAL(10,2) NULL,
    observacoes TEXT NULL,
    data_cadastro DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE receita_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receita_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    gramas_usadas DECIMAL(10,2) NOT NULL,
    custo_item DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (receita_id) REFERENCES receitas(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE custos_perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_cadastro DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE custos_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_id INT NOT NULL,
    etiqueta VARCHAR(100) NOT NULL,
    tipo ENUM('fixo', 'percentual') NOT NULL,
    valor_fixo DECIMAL(10,2) NULL,
    valor_percentual DECIMAL(5,2) NULL,
    base_calculo ENUM('custo', 'venda') NULL,
    FOREIGN KEY (perfil_id) REFERENCES custos_perfis(id) ON DELETE CASCADE
) ENGINE=InnoDB;
