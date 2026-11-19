<?php
require_once 'admin/config/sessao.php';
require_once 'admin/models/usuarios.php';

$erros = [];
$v = ['nome' => '', 'email' => '', 'telefone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = ['nome' => $_POST['nome'] ?? '', 'email' => $_POST['email'] ?? '', 'telefone' => $_POST['telefone'] ?? ''];
    $senha = $_POST['senha'] ?? '';

    if (!csrfValido($_POST['token'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } elseif ($senha !== ($_POST['senha2'] ?? '')) {
        $erros[] = 'A confirmação de senha não confere.';
    } else {
        // Quem se cadastra pela loja é SEMPRE usuário comum
        $novo = new Usuario($v + ['senha' => $senha, 'tipo' => 'user']);
        $erros = $novo->validar(true);
        if (!$erros) {
            try {
                $pdo->beginTransaction();
                if (!$novo->cadastrarUsuario()) throw new RuntimeException('Falha no cadastro.');
                $uid = (int)$pdo->lastInsertId();
                $stmt = $pdo->prepare('INSERT INTO clientes(usuario_id,nome,email,telefone) VALUES (?,?,?,?)');
                $stmt->execute([$uid,trim($v['nome']),trim($v['email']),trim($v['telefone'])]);
                $pdo->commit();
                header('Location: login.php?cadastrado=1'); exit;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $erros[] = 'Não foi possível cadastrar. Verifique se o e-mail já está em uso.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta — NextPlay</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261022">
</head>
<body>
    <?php require 'topo.php'; ?>
    <div class="form-box" style="max-width:400px">
        <h2>Criar conta</h2>
        <?php if ($erros): ?>
            <div class="alerta erro"><ul><?php foreach ($erros as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" maxlength="100" value="<?= e($v['nome']) ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" maxlength="150" value="<?= e($v['email']) ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" maxlength="25" value="<?= e($v['telefone']) ?>" required>

            <label for="senha">Senha (mínimo 6 caracteres):</label>
            <input type="password" id="senha" name="senha" minlength="6" required>

            <label for="senha2">Confirmar senha:</label>
            <input type="password" id="senha2" name="senha2" minlength="6" required>

            <button class="btn" type="submit">Cadastrar</button>
            <a class="voltar" href="login.php">Já tenho conta</a>
        </form>
    </div>
</body>
</html>
