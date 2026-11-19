-- NEXTPLAY: execute selecionando o banco loja_games no phpMyAdmin.
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
