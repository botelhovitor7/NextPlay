-- NEXTPLAY: instalação limpa. Importe este arquivo na página inicial do phpMyAdmin.
-- Cria banco próprio e todas as tabelas, com dados demonstrativos.

-- =====================================================================
-- GameStock 2025 — banco de dados loja_games
-- Pode ser importado quantas vezes quiser: NÃO apaga dados existentes
-- (usa IF NOT EXISTS e só insere os dados de teste se ainda não existirem).
-- Senhas de teste já estão com hash (password_hash / bcrypt):
--    evandro@gmail.com -> admin123 (administrador)
--    pedro@gmail.com   -> user123  (usuário comum)
-- =====================================================================
CREATE DATABASE IF NOT EXISTS nextplay_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nextplay_db;

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


-- Tabelas adicionais
-- NEXTPLAY: o banco nextplay_db ja foi selecionado acima.
-- Preserva dados anteriores; pode ser executado novamente.
CREATE TABLE IF NOT EXISTS categorias (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(80) NOT NULL UNIQUE,
 descricao VARCHAR(255) NOT NULL DEFAULT '', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO categorias(nome) SELECT DISTINCT categoria FROM games WHERE categoria IS NOT NULL AND TRIM(categoria) <> '';
CREATE TABLE IF NOT EXISTS clientes (
 id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NULL UNIQUE, nome VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE, telefone VARCHAR(25) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_cliente_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS pedidos (
 id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NULL, cliente_id INT NULL,
 total DECIMAL(12,2) NOT NULL DEFAULT 0, status ENUM('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_pedido_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
 CONSTRAINT fk_pedido_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS pedido_itens (
 id INT AUTO_INCREMENT PRIMARY KEY, pedido_id INT NOT NULL, game_id INT NULL,
 titulo VARCHAR(100) NOT NULL, quantidade INT NOT NULL, preco_unitario DECIMAL(10,2) NOT NULL,
 CONSTRAINT fk_item_pedido FOREIGN KEY(pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
 CONSTRAINT fk_item_game FOREIGN KEY(game_id) REFERENCES games(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Vincula os usuários comuns existentes aos clientes, sem duplicar.
INSERT IGNORE INTO clientes(usuario_id,nome,email,telefone)
 SELECT id,nome,email,'' FROM usuarios WHERE tipo='user';
