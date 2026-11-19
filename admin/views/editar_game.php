<?php
require_once '../config/sessao.php';
require_once '../models/games.php';

exigirAdmin('../../');

$game = new Game();
$dados = null;
$erros = [];

if (isset($_GET['id'])) {
    $dados = $game->buscarGamePorId((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = $game->buscarGamePorId((int)($_POST['id'] ?? 0));  // dados atuais (para manter a imagem)

    if (!$dados) {
        $erros[] = 'Jogo não encontrado.';
    } elseif (!csrfValido($_POST['token'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        $editar = new Game([
            'id'        => $dados['id'],
            'titulo'    => $_POST['titulo'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'preco'     => $_POST['preco'] ?? '',
            'estoque'   => $_POST['estoque'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'imagem'    => $dados['imagem']   // mantém a imagem atual, a menos que envie uma nova
        ]);
        $erros = $editar->validar();

        // Nova imagem é opcional
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $erroImg = '';
            $nova = salvarImagem($_FILES['imagem'], '../public/imagens/', $erroImg);
            if ($nova === null) {
                $erros[] = $erroImg;
            } else {
                $editar->imagem = $nova;
            }
        }

        if (!$erros) {
            if ($editar->editarGame()) {
                header('Location: ver_games.php?editado=1');
                exit;
            }
            $erros[] = 'Erro ao editar!';
        }
        // Em caso de erro, reexibe o que o usuário digitou
        $dados = array_merge($dados, [
            'titulo' => $editar->titulo, 'descricao' => $editar->descricao, 'preco' => $editar->preco,
            'estoque' => $editar->estoque, 'categoria' => $editar->categoria,
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Jogo</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="form-box">
        <h2>Editar Jogo</h2>

        <?php if ($erros): ?>
            <div class="alerta erro"><ul><?php foreach ($erros as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <?php if ($dados): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int)$dados['id'] ?>">
                <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">

                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" maxlength="100" value="<?= e($dados['titulo']) ?>" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4" required><?= e($dados['descricao']) ?></textarea>

                <label for="preco">Preço:</label>
                <input type="number" id="preco" step="0.01" min="0" name="preco" value="<?= e($dados['preco']) ?>" required>

                <label for="estoque">Estoque:</label>
                <input type="number" id="estoque" min="0" name="estoque" value="<?= e($dados['estoque']) ?>" required>

                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" maxlength="50" value="<?= e($dados['categoria']) ?>" required>

                <label>Imagem atual:</label>
                <div class="atual">
                    <img src="../public/imagens/<?= e($dados['imagem']) ?>" alt=""
                         onerror="this.onerror=null;this.src='../public/imagens/sem-imagem.jpg'">
                    <p><?= e($dados['imagem']) ?></p>
                </div>

                <label for="imagem">Trocar imagem (opcional):</label>
                <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

                <button class="btn" type="submit">Salvar</button>
                <a class="voltar" href="ver_games.php">Cancelar</a>
            </form>
        <?php else: ?>
            <p>Jogo não encontrado.</p>
            <a href="ver_games.php">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>
