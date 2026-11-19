<?php
require_once 'admin/config/sessao.php';
require_once 'admin/models/games.php';

$game = new Game();
$nome = trim($_GET['buscar'] ?? '');
$games = $nome ? $game->buscarPorNome($nome) : $game->listarGames();
$categorias = $pdo->query('SELECT nome FROM categorias ORDER BY nome')->fetchAll(PDO::FETCH_COLUMN);
$categoriaFiltro = trim((string)($_GET['categoria'] ?? ''));
if ($categoriaFiltro !== '') $games = array_values(array_filter($games, fn($g) => $g['categoria'] === $categoriaFiltro));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextPlay — Catálogo de Jogos</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261105">
</head>
<body>
    <?php require 'topo.php'; ?>
    <main class="container">
        <section class="np-hero">
            <div class="np-hero-copy">
                <div class="np-eyebrow"><span class="np-live"></span> NEXTPLAY / GAME DISCOVERY</div>
                <h1>O próximo nível <em>começa aqui<span>.</span></em></h1>
                <p>Novos mundos, histórias inesquecíveis e aquela próxima partida que você não vai querer encerrar.</p>
                <div class="np-hero-actions"><a href="#catalogo" class="btn azul">Explorar catálogo <span aria-hidden="true">↗</span></a><span>ESCOLHA. JOGUE. REPITA.</span></div>
            </div>
            <div class="np-hero-art" aria-hidden="true"><div class="np-orbit np-orbit-1"></div><div class="np-orbit np-orbit-2"></div><div class="np-core">N<span>↗</span></div><div class="np-art-label">READY FOR WHAT'S NEXT?</div><span class="np-art-plus">+</span></div>
            <div class="np-hero-index">NP / 001 — DIGITAL GAME STORE</div>
        </section>
        <div class="np-ticker" aria-label="Destaques da loja"><span>01 / DESCUBRA</span><span>JOGOS QUE MARCAM</span><span>✳</span><span>02 / ESCOLHA</span><span>SEU PRÓXIMO FAVORITO</span><span>✳</span><span>03 / JOGUE</span></div>
        <section id="catalogo" class="np-section-head secao-catalogo"><div><div class="np-eyebrow">// SUA PRÓXIMA AVENTURA</div><h2>Explore o <i>catálogo.</i></h2><p>Encontre o jogo certo para o seu próximo momento.</p></div><div class="np-section-mark">CATÁLOGO / NEXTPLAY</div></section>
        <?php if (($_GET['erro'] ?? '') === 'sem_permissao'): ?>
            <div class="alerta erro">Você não tem permissão para acessar a área administrativa.</div>
        <?php endif; ?>
        <?php if (isset($_GET['saiu'])): ?>
            <div class="alerta ok">Você saiu da sua conta.</div>
        <?php endif; ?>
        <?php if (isset($_GET['comprou'])): ?>
            <div class="alerta ok">Pedido registrado com sucesso! Aguardando confirmação de pagamento.</div>
        <?php endif; ?>

        <form class="busca" method="GET" action="index.php">
            <input type="text" name="buscar" placeholder="Buscar jogo por nome..." value="<?= e($nome) ?>">
            <select name="categoria" aria-label="Filtrar categoria"><option value="">Todas as categorias</option>
                <?php foreach ($categorias as $cat): ?><option value="<?= e($cat) ?>" <?= $categoriaFiltro===$cat?'selected':'' ?>><?= e($cat) ?></option><?php endforeach; ?>
            </select>
            <button class="btn azul" type="submit">Buscar</button>
        </form>

        <div class="grade np-product-grid">
            <?php foreach ($games as $g): ?>
                <a class="card np-product-card" href="detalhes.php?id=<?= (int)$g['id'] ?>">
                    <img src="admin/public/imagens/<?= e($g['imagem']) ?>" alt="<?= e($g['titulo']) ?>"
                         onerror="this.onerror=null;this.src='admin/public/imagens/sem-imagem.jpg'">
                    <div class="info">
                        <div class="np-card-kicker">NEXTPLAY SELECT / GAME</div><div class="nome"><?= e($g['titulo']) ?></div>
                        <div class="np-card-bottom"><div class="preco"><?= moeda($g['preco']) ?></div><span class="np-card-arrow">↗</span></div>
                        <?php if ((int)$g['estoque'] <= 0): ?><div class="esgotado">Esgotado</div><?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if (!$games): ?>
            <p style="text-align:center">Nenhum jogo encontrado.</p>
        <?php endif; ?>
    </main>
</body>
</html>
