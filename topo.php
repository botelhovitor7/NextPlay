<?php
/* Menu comum das páginas públicas (raiz do projeto). Incluído com require dentro do <body>. */
$qtdCarrinho = array_sum($_SESSION['carrinho'] ?? []);
?>
<div class="menu">
    <a class="logo" href="index.php"><span class="brand-symbol">N<span class="brand-arrow">↗</span></span><span class="brand-word">NEXT<span>PLAY</span><small>PLAY BEYOND.</small></span></a>
    <nav>
        <a href="index.php">Explorar jogos</a>
        <a href="carrinho.php">🛒 Carrinho (<?= (int)$qtdCarrinho ?>)</a>
        <?php if (usuarioLogado()): ?>
            <span class="ola">Olá, <?= e(usuarioLogado()['nome']) ?></span>
            <?php if (ehAdmin()): ?><a href="admin/views/gerenciar_games.php">Área administrativa</a><?php endif; ?>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="cadastro.php">Criar conta</a>
        <?php endif; ?>
    </nav>
</div>
