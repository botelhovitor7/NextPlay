<?php
require_once '../config/sessao.php';
require_once '../models/usuarios.php';

exigirAdmin('../../');

$usuarios = (new Usuario())->listarUsuarios();
$token = csrfToken();
$meuId = usuarioLogado()['id'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
    <script>
        function confirmarExclusao(nome, id) {
            if (confirm('Tem certeza que deseja excluir o usuário "' + nome + '"?')) {
                window.location.href = '../controllers/deletar_usuario.php?id=' + id + '&token=<?= e($token) ?>';
            }
        }
    </script>
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="container">
        <h2>Lista de Usuários</h2>

        <?php if (isset($_GET['deletado'])): ?><div class="alerta ok">Usuário excluído com sucesso!</div><?php endif; ?>
        <?php if (isset($_GET['editado'])): ?><div class="alerta ok">Usuário editado com sucesso!</div><?php endif; ?>
        <?php if (isset($_GET['cadastrado'])): ?><div class="alerta ok">Usuário cadastrado com sucesso!</div><?php endif; ?>
        <?php if (($_GET['erro'] ?? '') === 'autoexclusao'): ?><div class="alerta erro">Você não pode excluir o seu próprio usuário.</div><?php endif; ?>
        <?php if (($_GET['erro'] ?? '') === 'ultimo_admin'): ?><div class="alerta erro">Não é possível excluir o último administrador.</div><?php endif; ?>

        <div class="top-bar">
            <span></span>
            <a class="btn" href="cadastrar_usuario.php">+ Cadastrar Novo Usuário</a>
        </div>

        <div class="tabela-rolavel">
        <table>
            <tr>
                <th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Criado em</th><th>Ações</th>
            </tr>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= e($u['nome']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= $u['tipo'] === 'admin' ? 'Administrador' : 'Usuário comum' ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($u['created_at']))) ?></td>
                    <td class="actions">
                        <a href="editar_usuario.php?id=<?= (int)$u['id'] ?>" title="Editar">&#9998;</a>
                        <?php if ((int)$u['id'] !== $meuId): ?>
                            <a class="excluir" href="#" title="Excluir"
                               onclick="confirmarExclusao('<?= e(addslashes($u['nome'])) ?>', <?= (int)$u['id'] ?>); return false;">&#10060;</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    </div>
</body>
</html>
