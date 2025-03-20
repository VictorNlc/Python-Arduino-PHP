-- Criar o banco de dados 'barbearia' (se não existir)
CREATE DATABASE IF NOT EXISTS barbearia;

-- Usar o banco de dados 'barbearia'
USE barbearia;

-- Criar a tabela 'clientes'
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario VARCHAR(255) NOT NULL -- Removida a vírgula extra
);

-- Criar a tabela 'servicos'
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL -- Adicionado NOT NULL para maior consistência
);

-- Criar a tabela 'agendamentos'
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE -- Removida a vírgula extra
);

-- Inserir alguns serviços de exemplo
INSERT INTO servicos (nome, descricao, preco) VALUES
('Corte de Cabelo', 'Corte de cabelo masculino completo', 30.00),
('Barba', 'Barba completa com toalha quente', 25.00),
('Corte e Barba', 'Pacote completo: corte de cabelo e barba', 50.00);
