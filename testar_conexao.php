<?php
// Teste de conexão do material do professor (Capítulo 3).
// Acesse: http://localhost/lojagames_next1/testar_conexao.php
require_once 'admin/config/conexao.php';

$sql = "SELECT * FROM games";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Jogos</title>
    <link rel="stylesheet" href="admin/public/css/style.css?v=20261022">
</head>
<body>
    <h2>Lista de Jogos</h2>
    <p style="text-align:center;color:green">✔ Conexão com o banco realizada com sucesso!</p>
    <table>
        <tr><th>ID</th><th>Título</th><th>Descrição</th><th>Preço</th><th>Estoque</th><th>Categoria</th><th>Imagem (Nome)</th></tr>
        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= (int)$game['id'] ?></td>
                    <td><?= htmlspecialchars($game['titulo']) ?></td>
                    <td><?= htmlspecialchars($game['descricao']) ?></td>
                    <td>R$ <?= number_format($game['preco'], 2, ',', '.') ?></td>
                    <td><?= (int)$game['estoque'] ?></td>
                    <td><?= htmlspecialchars($game['categoria']) ?></td>
                    <td><?= htmlspecialchars($game['imagem']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Nenhum jogo cadastrado.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
