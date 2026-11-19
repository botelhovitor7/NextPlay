<?php
/*
 * SESSÃO, AUTENTICAÇÃO E FUNÇÕES AUXILIARES
 * Funções simples (sem classes extras) usadas por todas as páginas.
 * Os redirecionamentos usam caminho relativo; o parâmetro $raiz é o caminho
 * da página atual até a pasta loja_games ('' na raiz, '../../' nas views do admin).
 */
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Dados do usuário logado (ou null)
function usuarioLogado() {
    return $_SESSION['usuario'] ?? null;
}

function ehAdmin() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin';
}

// Guarda o usuário na sessão (a senha nunca vai para a sessão)
function fazerLogin($usuario) {
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'    => (int)$usuario['id'],
        'nome'  => $usuario['nome'],
        'email' => $usuario['email'],
        'tipo'  => $usuario['tipo'],
    ];
    $_SESSION['token'] = bin2hex(random_bytes(16));
}

function fazerLogout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// Protege páginas: precisa estar logado
function exigirLogin($raiz = '') {
    if (!usuarioLogado()) {
        header('Location: ' . $raiz . 'login.php?erro=restrito');
        exit;
    }
}

// Protege páginas: precisa ser administrador
function exigirAdmin($raiz = '') {
    exigirLogin($raiz);
    if (!ehAdmin()) {
        header('Location: ' . $raiz . 'index.php?erro=sem_permissao');
        exit;
    }
}

// Token anti-CSRF (formulários e links de exclusão)
function csrfToken() {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['token'];
}

function csrfValido($token) {
    return is_string($token) && !empty($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

// Salva imagem enviada por upload. Retorna o nome do arquivo ou null (com $erro preenchido).
function salvarImagem($arquivo, $pasta, &$erro) {
    if (!isset($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Falha no envio da imagem.';
        return null;
    }
    if ($arquivo['size'] > 3 * 1024 * 1024) {
        $erro = 'A imagem deve ter no máximo 3 MB.';
        return null;
    }
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) || @getimagesize($arquivo['tmp_name']) === false) {
        $erro = 'Envie uma imagem válida (jpg, png, gif ou webp).';
        return null;
    }
    $nome = uniqid('game_', true) . '.' . $ext;
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nome)) {
        $erro = 'Não foi possível salvar a imagem na pasta admin/public/imagens.';
        return null;
    }
    return $nome;
}
