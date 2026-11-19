<?php
/*
 * CARRINHO SIMPLES (sem classes extras): fica guardado na sessão como
 * $_SESSION['carrinho'] = [ id_do_jogo => quantidade ].
 * A finalização registra pedido e itens em transação e baixa o estoque.
 */
require_once 'admin/config/sessao.php';
require_once 'admin/models/games.php';

$gameModel = new Game();
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!csrfValido($_POST['token'] ?? '')) {
        $erro = 'Sessão expirada. Tente novamente.';
    } elseif ($acao === 'adicionar' && $id) {
        $g = $gameModel->buscarGamePorId($id);
        $qtd = max(1, (int)($_POST['qtd'] ?? 1));
        if (!$g) {
            $erro = 'Jogo não encontrado.';
        } else {
            $novaQtd = ($_SESSION['carrinho'][$id] ?? 0) + $qtd;
            if ($novaQtd > (int)$g['estoque']) {
                $erro = 'Estoque insuficiente para "' . $g['titulo'] . '" (disponível: ' . (int)$g['estoque'] . ').';
            } else {
                $_SESSION['carrinho'][$id] = $novaQtd;
                $sucesso = '"' . $g['titulo'] . '" foi adicionado ao carrinho.';
            }
        }
    } elseif ($acao === 'remover' && $id) {
        unset($_SESSION['carrinho'][$id]);
        $sucesso = 'Item removido do carrinho.';
    } elseif ($acao === 'limpar') {
        $_SESSION['carrinho'] = [];
        $sucesso = 'Carrinho esvaziado.';
    } elseif ($acao === 'finalizar') {
        if (!usuarioLogado()) {
            header('Location: login.php?redir=carrinho&erro=restrito');
            exit;
        }
        require_once 'admin/models/pedidos.php';
        try {
            (new Pedido())->finalizarCarrinho((int)usuarioLogado()['id'], $_SESSION['carrinho']);
            $_SESSION['carrinho'] = [];
            header('Location: index.php?comprou=1'); exit;
        } catch (Throwable $e) {
            $erro = 'Não foi possível finalizar a compra: ' . $e->getMessage();
        }
    }
}

// Monta os itens com os dados atuais do banco (remove jogos que não existem mais)
$itens = [];
$total = 0;
foreach ($_SESSION['carrinho'] as $gid => $q) {
    $g = $gameModel->buscarGamePorId((int)$gid);
    if (!$g) {
        unset($_SESSION['carrinho'][$gid]);
        continue;
    }
    $sub = $g['preco'] * $q;
    $total += $sub;
    $itens[] = ['g' => $g, 'qtd' => $q, 'sub' => $sub];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho — NextPlay</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261022">
</head>
<body>
    <?php require 'topo.php'; ?>
    <div class="container">
        <h2>Meu Carrinho</h2>

        <?php if ($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="alerta ok"><?= e($sucesso) ?></div><?php endif; ?>

        <?php if (!$itens): ?>
            <p style="text-align:center">Seu carrinho está vazio.</p>
            <p style="text-align:center"><a class="btn azul" href="index.php">Ver catálogo</a></p>
        <?php else: ?>
            <div class="tabela-rolavel">
            <table>
                <tr><th>Jogo</th><th>Preço</th><th>Quantidade</th><th>Subtotal</th><th>Remover</th></tr>
                <?php foreach ($itens as $i): ?>
                    <tr>
                        <td>
                            <img class="mini" src="admin/public/imagens/<?= e($i['g']['imagem']) ?>" alt=""
                                 onerror="this.onerror=null;this.src='admin/public/imagens/sem-imagem.jpg'">
                            <a href="detalhes.php?id=<?= (int)$i['g']['id'] ?>"><?= e($i['g']['titulo']) ?></a>
                        </td>
                        <td><?= moeda($i['g']['preco']) ?></td>
                        <td><?= (int)$i['qtd'] ?></td>
                        <td><?= moeda($i['sub']) ?></td>
                        <td>
                            <form method="POST" style="margin:0">
                                <input type="hidden" name="acao" value="remover">
                                <input type="hidden" name="id" value="<?= (int)$i['g']['id'] ?>">
                                <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">
                                <button class="btn vermelho" type="submit">X</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>

            <div class="total"><strong>Total: <?= moeda($total) ?></strong></div>

            <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap">
                <a class="btn cinza" href="index.php">Continuar comprando</a>
                <form method="POST" style="margin:0">
                    <input type="hidden" name="acao" value="limpar">
                    <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">
                    <button class="btn vermelho" type="submit">Esvaziar carrinho</button>
                </form>
                <form method="POST" style="margin:0">
                    <input type="hidden" name="acao" value="finalizar">
                    <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">
                    <button class="btn" type="submit">Registrar pedido (sem pagamento online)</button>
                </form>
            </div>
            <?php if (!usuarioLogado()): ?>
                <p style="text-align:right"><small>Para finalizar a compra é necessário entrar na sua conta.</small></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
