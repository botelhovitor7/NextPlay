<?php
require_once '../config/sessao.php';
require_once '../models/games.php';

// Somente administrador logado pode excluir
exigirAdmin('../../');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!csrfValido($_GET['token'] ?? '')) {
    echo "Requisição inválida.";
    exit;
}

if ($id) {
    $game = new Game(['id' => $id]);
    if ($game->deletarGame()) {
        header('Location: ../views/ver_games.php?deletado=1');
        exit;
    } else {
        echo "Erro ao excluir o jogo.";
    }
} else {
    echo "ID do jogo não fornecido.";
}
