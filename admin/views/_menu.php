<?php /* Menu comum das páginas administrativas. Incluído com require dentro do <body>. */ ?>
<div class="menu">
    <a class="logo" href="gerenciar_games.php">🎮 NextPlay — Admin</a>
    <nav>
        <a href="gerenciar_games.php">Painel</a>
        <a href="ver_games.php">Jogos</a>
        <a href="ver_usuarios.php">Usuários</a>
        <a href="clientes.php">Clientes</a>
        <a href="categorias.php">Categorias</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="../../index.php">Ver loja</a>
        <span class="ola">Olá, <?= e(usuarioLogado()['nome']) ?></span>
        <a href="../../logout.php">Sair</a>
    </nav>
</div>
