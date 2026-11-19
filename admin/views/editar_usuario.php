<?php
require_once '../config/sessao.php';
require_once '../models/usuarios.php';

exigirAdmin('../../');

$modelo = new Usuario();
$dados = null;
$erros = [];
$meuId = usuarioLogado()['id'];

if (isset($_GET['id'])) {
    $dados = $modelo->buscarUsuarioPorId((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = $modelo->buscarUsuarioPorId((int)($_POST['id'] ?? 0));

    if (!$dados) {
        $erros[] = 'Usuário não encontrado.';
    } elseif (!csrfValido($_POST['token'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        // O administrador não pode mudar o próprio tipo (evita ficar sem nenhum admin)
        $tipo = ((int)$dados['id'] === $meuId) ? $dados['tipo'] : ($_POST['tipo'] ?? 'user');

        $editar = new Usuario([
            'id'    => $dados['id'],
            'nome'  => $_POST['nome'] ?? '',
            'email' => $_POST['email'] ?? '',
            'senha' => $_POST['senha'] ?? '',   // vazio = mantém a senha atual
            'tipo'  => $tipo,
        ]);
        $erros = $editar->validar(false);

        if (!$erros) {
            if ($editar->editarUsuario()) {
                // Se editou a si mesmo, atualiza o nome/e-mail da sessão
                if ((int)$dados['id'] === $meuId) {
                    $_SESSION['usuario']['nome'] = $editar->nome;
                    $_SESSION['usuario']['email'] = $editar->email;
                }
                header('Location: ver_usuarios.php?editado=1');
                exit;
            }
            $erros[] = 'Erro ao editar!';
        }
        $dados = array_merge($dados, ['nome' => $editar->nome, 'email' => $editar->email, 'tipo' => $tipo]);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="form-box">
        <h2>Editar Usuário</h2>
        <?php if ($erros): ?>
            <div class="alerta erro"><ul><?php foreach ($erros as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <?php if ($dados): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= (int)$dados['id'] ?>">
                <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" maxlength="100" value="<?= e($dados['nome']) ?>" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" maxlength="150" value="<?= e($dados['email']) ?>" required>

                <label for="senha">Nova senha (deixe em branco para manter a atual):</label>
                <input type="password" id="senha" name="senha" minlength="6">

                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" <?= (int)$dados['id'] === $meuId ? 'disabled' : '' ?>>
                    <option value="user"  <?= $dados['tipo'] === 'user' ? 'selected' : '' ?>>Usuário comum</option>
                    <option value="admin" <?= $dados['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
                <?php if ((int)$dados['id'] === $meuId): ?>
                    <p><small>Você não pode alterar o seu próprio tipo.</small></p>
                <?php endif; ?>

                <button class="btn" type="submit">Salvar</button>
                <a class="voltar" href="ver_usuarios.php">Cancelar</a>
            </form>
        <?php else: ?>
            <p>Usuário não encontrado.</p>
            <a href="ver_usuarios.php">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>
