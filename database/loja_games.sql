-- =====================================================================
-- GameStock 2025 — banco de dados loja_games
-- Pode ser importado quantas vezes quiser: NÃO apaga dados existentes
-- (usa IF NOT EXISTS e só insere os dados de teste se ainda não existirem).
-- Senhas de teste já estão com hash (password_hash / bcrypt):
--    evandro@gmail.com -> admin123 (administrador)
--    pedro@gmail.com   -> user123  (usuário comum)
-- =====================================================================
CREATE DATABASE IF NOT EXISTS loja_games CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE loja_games;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de games
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT DEFAULT 0,
    categoria VARCHAR(50),
    imagem VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Dados de teste: usuários (INSERT IGNORE: se o e-mail já existir, não duplica)
INSERT IGNORE INTO usuarios (nome, email, senha, tipo) VALUES
('evandro', 'evandro@gmail.com', '$2y$10$9Npv8jnM9/HijamQWyzaeuIR/JVpaurvu4MrqP6ACPeopXg96PXaG', 'admin'),
('pedro',   'pedro@gmail.com',   '$2y$10$Zx39/wWCUpujbFPiIQsGKeJsyO6t9kPSYcr6o1WJNW/Vt1n5V6upu', 'user');

-- Dados de teste: games (só insere se o título ainda não existir)
INSERT INTO games (titulo, descricao, preco, estoque, categoria, imagem)
SELECT * FROM (SELECT 'The Legend of Zelda: Breath of the Wild' AS titulo, 'Um jogo de aventura em mundo aberto onde Link desperta em Hyrule para derrotar Calamity Ganon.' AS descricao, 299.99 AS preco, 10 AS estoque, 'Aventura' AS categoria, 'zelda.jpg' AS imagem) t
WHERE NOT EXISTS (SELECT 1 FROM games WHERE titulo = 'The Legend of Zelda: Breath of the Wild');

INSERT INTO games (titulo, descricao, preco, estoque, categoria, imagem)
SELECT * FROM (SELECT 'God of War' AS titulo, 'Jogo de ação com Kratos em uma jornada épica pela mitologia nórdica.' AS descricao, 249.99 AS preco, 15 AS estoque, 'Ação' AS categoria, 'gow.jpg' AS imagem) t
WHERE NOT EXISTS (SELECT 1 FROM games WHERE titulo = 'God of War');

INSERT INTO games (titulo, descricao, preco, estoque, categoria, imagem)
SELECT * FROM (SELECT 'Minecraft' AS titulo, 'Jogo de construção e sobrevivência em mundo aberto.' AS descricao, 129.99 AS preco, 20 AS estoque, 'Sandbox' AS categoria, 'minecraft.jpg' AS imagem) t
WHERE NOT EXISTS (SELECT 1 FROM games WHERE titulo = 'Minecraft');
