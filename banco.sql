CREATE DATABASE IF NOT EXISTS matrixedu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE matrixedu;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','professor') NOT NULL DEFAULT 'professor',
    licenca ENUM('pendente','liberada','bloqueada') NOT NULL DEFAULT 'pendente',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS carrinhos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    total INT NOT NULL,
    em_uso INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carrinho_id INT NOT NULL,
    sala VARCHAR(20) NOT NULL,
    professor VARCHAR(100) DEFAULT NULL,
    quantidade INT NOT NULL,
    acao ENUM('retirada','devolucao') NOT NULL,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_carrinho FOREIGN KEY (carrinho_id) REFERENCES carrinhos(id)
);

INSERT INTO carrinhos (id,nome,total,em_uso) VALUES
(1,'Carrinho Lenovo',27,0),
(2,'Carrinho Positivo',30,0),
(3,'Carrinho Tablets',29,0)
ON DUPLICATE KEY UPDATE nome=VALUES(nome), total=VALUES(total);

-- Senha do administrador: matrix2026
INSERT INTO usuarios (nome,email,senha,tipo,licenca)
SELECT 'Administrador(a)','admin@matrixedu.com', '$2y$12$ycXp4k00rspM1OHfeuHAAOg5L2SofFijag/PDon8E9HMM7b4oS7pO', 'admin','liberada'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='admin@matrixedu.com');
