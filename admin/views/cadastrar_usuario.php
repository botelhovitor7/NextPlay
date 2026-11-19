<?php
require_once '../config/sessao.php';
require_once '../models/usuarios.php';

exigirAdmin('../../');

$erros = [];
$v = ['nome' => '', 'email' => '', 'tipo' => 'user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = ['nome' => $_POST['nome'] ?? '', 'email' => $_POST['email'] ?? '', 'tipo' => $_POST['tipo'] ?? 'user'];

    if (!csrfValido($_POST['token'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        $novo = new Usuario($v + ['senha' => $_POST['senha'] ?? '']);
        $erros = $novo->validar(true);
        if (!$erros) {
            if ($novo->cadastrarUsuario()) {
                header('Location: ver_usuarios.php?cadastrado=1');
                exit;
            }
            $erros[] = 'Erro ao cadastrar no banco de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="form-box">
        <h2>Cadastrar Novo Usuário</h2>
        <?php if ($erros): ?>
            <div class="alerta erro"><ul><?php foreach ($erros as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" maxlength="100" value="<?= e($v['nome']) ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" maxlength="150" value="<?= e($v['email']) ?>" required>

            <label for="senha">Senha (mínimo 6 caracteres):</label>
            <input type="password" id="senha" name="senha" minlength="6" required>

            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo">
                <option value="user"  <?= $v['tipo'] === 'user' ? 'selected' : '' ?>>Usuário comum</option>
                <option value="admin" <?= $v['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <button class="btn" type="submit">Cadastrar</button>
            <a class="voltar" href="ver_usuarios.php">Voltar</a>
        </form>
    </div>
</body>
</html>
