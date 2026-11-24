
CREATE DATABASE IF NOT EXISTS infinity_mobile
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE infinity_mobile;

-- TABELA DE USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'
);

-- TABELA DE CATEGORIAS
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- TABELA DE PRODUTOS
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- TABELA DE PEDIDOS
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    forma_pagamento ENUM('PIX', 'Boleto', 'Cartão') NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- TABELA DE ITENS DO PEDIDO
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- CATEGORIAS
INSERT INTO categorias (nome) VALUES
('iPhone 13'),
('iPhone 14'),
('iPhone 15'),
('iPhone 16'),
('iPhone 17');

-- 15 PRODUTOS
INSERT INTO produtos
(categoria_id, nome, descricao, preco, estoque, imagem)
VALUES
(1, 'iPhone 13 128GB',
 'iPhone 13 com 128GB de armazenamento.',
 2999.90, 10, 'iphone13.jpg'),

(1, 'iPhone 13 256GB',
 'iPhone 13 com 256GB de armazenamento.',
 3399.90, 8, 'iphone13-256.jpg'),

(1, 'iPhone 13 Pro 128GB',
 'iPhone 13 Pro com camera tripla.',
 3799.90, 6, 'iphone13-pro.jpg'),

(2, 'iPhone 14 128GB',
 'iPhone 14 com camera dupla.',
 3499.90, 10, 'iphone14.jpg'),

(2, 'iPhone 14 Plus 128GB',
 'iPhone 14 Plus com tela de 6,7 polegadas.',
 3999.90, 8, 'iphone14-plus.jpg'),

(2, 'iPhone 14 Pro 128GB',
 'iPhone 14 Pro com Dynamic Island.',
 4499.90, 6, 'iphone14-pro.jpg'),

(3, 'iPhone 15 128GB',
 'iPhone 15 com USB-C e Dynamic Island.',
 4199.90, 10, 'iphone15.jpg'),

(3, 'iPhone 15 Plus 128GB',
 'iPhone 15 Plus com tela maior.',
 4699.90, 8, 'iphone15-plus.jpg'),

(3, 'iPhone 15 Pro 128GB',
 'iPhone 15 Pro com chip A17 Pro.',
 5299.90, 6, 'iphone15-pro.jpg'),

(4, 'iPhone 16 128GB',
 'iPhone 16 com chip A18.',
 4999.90, 10, 'iphone16.jpg'),

(4, 'iPhone 16 Plus 128GB',
 'iPhone 16 Plus com tela de 6,7 polegadas.',
 5499.90, 8, 'iphone16-plus.jpg'),

(4, 'iPhone 16 Pro 128GB',
 'iPhone 16 Pro com camera avancada.',
 6199.90, 6, 'iphone16-pro.jpg'),

(5, 'iPhone 17 256GB',
 'iPhone 17 com 256GB de armazenamento.',
 5999.90, 10, 'iphone17.jpg'),

(5, 'iPhone 17 Pro 256GB',
 'iPhone 17 Pro com cameras profissionais.',
 7499.90, 8, 'iphone17-pro.jpg'),

(5, 'iPhone 17 Pro Max 256GB',
 'iPhone 17 Pro Max com tela grande.',
 8499.90, 5, 'iphone17-pro-max.jpg');
