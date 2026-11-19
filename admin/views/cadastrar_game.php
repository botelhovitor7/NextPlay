<?php
require_once '../config/sessao.php';
require_once '../models/games.php';

exigirAdmin('../../');

$mensagem = '';
$erros = [];
$v = ['titulo' => '', 'descricao' => '', 'preco' => '', 'estoque' => '', 'categoria' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $campo => $_) {
        $v[$campo] = $_POST[$campo] ?? '';
    }

    if (!csrfValido($_POST['token'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente novamente.';
    } else {
        $novo = new Game($v + ['imagem' => null]);
        $erros = $novo->validar();

        // A imagem só é salva se os demais dados estiverem válidos
        if (!$erros) {
            $erroImg = '';
            $imagemNome = salvarImagem($_FILES['imagem'] ?? null, '../public/imagens/', $erroImg);
            if ($imagemNome === null) {
                $erros[] = $erroImg;
            }
        }

        if (!$erros) {
            $novo->imagem = $imagemNome;
            if ($novo->cadastrarGame()) {
                $mensagem = 'sucesso';
                $v = ['titulo' => '', 'descricao' => '', 'preco' => '', 'estoque' => '', 'categoria' => ''];
            } else {
                $erros[] = 'Erro ao cadastrar no banco de dados.';
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
    <title>Cadastrar Jogo</title>
    <link rel="stylesheet" href="../public/css/style.css?v=20261022">
</head>
<body>
    <?php require '_menu.php'; ?>
    <div class="form-box">
        <h2>Cadastrar Novo Jogo</h2>

        <?php if ($mensagem === 'sucesso'): ?>
            <div class="alerta ok" id="mensagem">Jogo cadastrado com sucesso!</div>
            <script>
                setTimeout(() => { document.getElementById('mensagem').style.display = 'none'; }, 3000);
            </script>
        <?php endif; ?>
        <?php if ($erros): ?>
            <div class="alerta erro"><ul><?php foreach ($erros as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= e(csrfToken()) ?>">

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" maxlength="100" value="<?= e($v['titulo']) ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required><?= e($v['descricao']) ?></textarea>

            <label for="preco">Preço:</label>
            <input type="number" id="preco" step="0.01" min="0" name="preco" value="<?= e($v['preco']) ?>" required>

            <label for="estoque">Estoque:</label>
            <input type="number" id="estoque" min="0" name="estoque" value="<?= e($v['estoque']) ?>" required>

            <label for="categoria">Categoria:</label>
            <input type="text" id="categoria" name="categoria" maxlength="50" value="<?= e($v['categoria']) ?>" required>

            <label for="imagem">Imagem:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required><br><br>

            <button class="btn" type="submit">Cadastrar</button>
            <a class="voltar" href="ver_games.php">Voltar</a>
        </form>
    </div>
</body>
</html>
