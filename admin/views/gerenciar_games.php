<?php
require_once '../config/sessao.php';
require_once '../models/games.php';
require_once '../models/usuarios.php';

exigirAdmin('../../');

$totalGames    = (new Game())->contarGames();
$totalUsuarios = count((new Usuario())->listarUsuarios());
$totalClientes = (int)$pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$totalPedidos = (int)$pdo->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="container">
        <h2>Painel Administrativo</h2>
        <div class="painel">
            <div class="caixa">
                <div class="numero"><?= $totalGames ?></div>
                <p>jogos cadastrados</p>
                <a class="btn azul" href="ver_games.php">Gerenciar jogos</a>
                <a class="btn" href="cadastrar_game.php">+ Novo jogo</a>
            </div>
            <div class="caixa">
                <div class="numero"><?= $totalUsuarios ?></div>
                <p>usuários cadastrados</p>
                <a class="btn azul" href="ver_usuarios.php">Gerenciar usuários</a>
                <a class="btn" href="cadastrar_usuario.php">+ Novo usuário</a>
            </div>
            <div class="caixa"><div class="numero"><?= $totalClientes ?></div><p>clientes cadastrados</p><a class="btn azul" href="clientes.php">Gerenciar clientes</a></div>
            <div class="caixa"><div class="numero"><?= $totalPedidos ?></div><p>pedidos registrados</p><a class="btn azul" href="pedidos.php">Gerenciar pedidos</a></div>
        </div>
    </div>
</body>
</html>
