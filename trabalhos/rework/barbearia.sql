-- Create the 'barbearia' database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS barbearia;

-- Use the 'barbearia' database
USE barbearia;

-- Create the 'clientes' table
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
    tipo_usuario VARCHAR(255) NOT NULL
);

-- Create the 'servicos' table (if you have different services)
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2)
);

-- Create the 'agendamentos' table
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,  -- This remains an INT referencing servicos.id
    data_hora DATETIME NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- Insert some example services (optional)
INSERT INTO servicos (nome, descricao, preco) VALUES
('Corte de Cabelo', 'Corte de cabelo masculino completo', 30.00),
('Barba', 'Barba completa com toalha quente', 25.00),
('Corte e Barba', 'Pacote completo: corte de cabelo e barba', 50.00);

-- Example of how to get the service ID (you'll do this in your PHP)
-- SELECT id FROM servicos WHERE nome = 'Corte de Cabelo';