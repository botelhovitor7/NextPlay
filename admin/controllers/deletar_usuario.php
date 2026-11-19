<?php
require_once '../config/sessao.php';
require_once '../models/usuarios.php';

// Somente administrador logado pode excluir
exigirAdmin('../../');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!csrfValido($_GET['token'] ?? '')) {
    echo "Requisição inválida.";
    exit;
}
if (!$id) {
    echo "ID do usuário não fornecido.";
    exit;
}

// Regras de segurança: não se excluir e não excluir o último administrador
if ($id === usuarioLogado()['id']) {
    header('Location: ../views/ver_usuarios.php?erro=autoexclusao');
    exit;
}

$usuario = new Usuario(['id' => $id]);
$alvo = $usuario->buscarUsuarioPorId($id);
if ($alvo && $alvo['tipo'] === 'admin' && $usuario->contarAdmins() <= 1) {
    header('Location: ../views/ver_usuarios.php?erro=ultimo_admin');
    exit;
}

if ($usuario->deletarUsuario()) {
    header('Location: ../views/ver_usuarios.php?deletado=1');
    exit;
} else {
    echo "Erro ao excluir o usuário.";
}
