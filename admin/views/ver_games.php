<?php
require_once '../config/sessao.php';
require_once '../models/games.php';

exigirAdmin('../../');

$game = new Game();

$nome = trim($_GET['buscar'] ?? '');
$games = $nome ? $game->buscarPorNome($nome) : $game->listarGames();
$token = csrfToken();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Jogos</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
    <script>
        function confirmarExclusao(titulo, id) {
            if (confirm('Tem certeza que deseja excluir o jogo "' + titulo + '"?')) {
                window.location.href = '../controllers/deletar_game.php?id=' + id + '&token=<?= e($token) ?>';
            }
        }
    </script>
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="container">
        <h2>Lista de Jogos</h2>

        <?php if (isset($_GET['deletado'])): ?>
            <div class="alerta ok">Jogo excluído com sucesso!</div>
        <?php endif; ?>
        <?php if (isset($_GET['editado'])): ?>
            <div class="alerta ok">Jogo editado com sucesso!</div>
        <?php endif; ?>

        <div class="top-bar">
            <form method="GET" action="">
                <input type="text" name="buscar" placeholder="Buscar por nome..." value="<?= e($nome) ?>">
                <button class="btn azul" type="submit">Buscar</button>
            </form>
            <a class="btn" href="cadastrar_game.php">+ Cadastrar Novo Jogo</a>
        </div>

        <div class="tabela-rolavel">
        <table>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Categoria</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
            <?php if (count($games) > 0): ?>
                <?php foreach ($games as $g): ?>
                    <tr>
                        <td><?= (int)$g['id'] ?></td>
                        <td><?= e($g['titulo']) ?></td>
                        <td><?= e(cortar($g['descricao'], 80)) ?></td>
                        <td><?= moeda($g['preco']) ?></td>
                        <td><?= (int)$g['estoque'] ?></td>
                        <td><?= e($g['categoria']) ?></td>
                        <td>
                            <img class="mini" src="../public/imagens/<?= e($g['imagem']) ?>" alt=""
                                 onerror="this.onerror=null;this.src='../public/imagens/sem-imagem.jpg'">
                        </td>
                        <td class="actions">
                            <a href="editar_game.php?id=<?= (int)$g['id'] ?>" title="Editar">&#9998;</a>
                            <a class="excluir" href="#" title="Excluir"
                               onclick="confirmarExclusao('<?= e(addslashes($g['titulo'])) ?>', <?= (int)$g['id'] ?>); return false;">&#10060;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Nenhum jogo encontrado.</td></tr>
            <?php endif; ?>
        </table>
        </div>
    </div>
</body>
</html>
