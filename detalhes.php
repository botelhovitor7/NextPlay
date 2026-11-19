<?php
require_once 'admin/config/sessao.php';
require_once 'admin/models/games.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$g = $id ? (new Game())->buscarGamePorId($id) : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $g ? e($g['titulo']) : 'Jogo não encontrado' ?> — NextPlay</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261022">
</head>
<body>
    <?php require 'topo.php'; ?>
    <div class="container">
        <?php if (!$g): ?>
            <div class="alerta erro">Jogo não encontrado.</div>
            <a class="btn cinza" href="index.php">← Voltar ao catálogo</a>
        <?php else: ?>
            <p><a href="index.php">← Voltar ao catálogo</a></p>
            <div class="detalhes">
                <div class="foto">
                    <img src="admin/public/imagens/<?= e($g['imagem']) ?>" alt="<?= e($g['titulo']) ?>"
                         onerror="this.onerror=null;this.src='admin/public/imagens/sem-imagem.jpg'">
                </div>
                <div class="dados">
                    <h1><?= e($g['titulo']) ?></h1>
                    <span class="etiqueta"><?= e($g['categoria']) ?></span>
                    <p><?= nl2br(e($g['descricao'])) ?></p>
                    <div class="preco-grande"><?= moeda($g['preco']) ?></div>
                    <p>
                        <?php if ((int)$g['estoque'] > 0): ?>
                            <strong>Em estoque:</strong> <?= (int)$g['estoque'] ?> unidade(s)
                        <?php else: ?>
                            <strong style="color:#dc3545">Esgotado</strong>
                        <?php endif; ?>
                    </p>

                    <?php if ((int)$g['estoque'] > 0): ?>
                        <form method="POST" action="carrinho.php">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                            <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">
                            <label>Quantidade:
                                <input type="number" name="qtd" value="1" min="1" max="<?= (int)$g['estoque'] ?>">
                            </label>
                            <button class="btn" type="submit">🛒 Adicionar ao carrinho</button>
                        </form>
                    <?php endif; ?>
                    <a class="btn cinza" href="index.php">Voltar ao catálogo</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
