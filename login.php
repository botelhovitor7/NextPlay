<?php
require_once 'admin/config/sessao.php';
require_once 'admin/models/usuarios.php';

// Só aceitamos voltar para estas páginas após o login (evita redirecionamento malicioso)
$redir = ($_GET['redir'] ?? $_POST['redir'] ?? '') === 'carrinho' ? 'carrinho' : '';

function destinoAposLogin($redir) {
    if (ehAdmin()) {
        return 'admin/views/gerenciar_games.php';   // administrador -> área administrativa
    }
    return $redir === 'carrinho' ? 'carrinho.php' : 'index.php';  // usuário comum -> loja
}

// Já está logado: manda direto para o destino
if (usuarioLogado() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . destinoAposLogin($redir));
    exit;
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $u = (new Usuario())->autenticar($email, $senha);
        if ($u) {
            fazerLogin($u);
            header('Location: ' . destinoAposLogin($redir));
            exit;
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — NextPlay</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261022">
</head>
<body>
    <?php require 'topo.php'; ?>
    <div class="form-box" style="max-width:400px">
        <h2>Login</h2>

        <?php if ($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
        <?php if (($_GET['erro'] ?? '') === 'restrito'): ?>
            <div class="alerta erro">Você precisa entrar para acessar essa página.</div>
        <?php endif; ?>
        <?php if (isset($_GET['cadastrado'])): ?>
            <div class="alerta ok">Conta criada com sucesso! Faça o login.</div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="redir" value="<?= e($redir) ?>">

            <label for="email">Usuário (e-mail):</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <button class="btn" type="submit">Entrar</button>
            <a class="voltar" href="cadastro.php">Criar conta</a>
        </form>
    </div>
</body>
</html>
