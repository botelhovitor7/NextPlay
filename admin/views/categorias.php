<?php require_once __DIR__.'/../controllers/categorias.php'; ?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Categorias — NextPlay</title><link rel="stylesheet" href="../public/css/style.css"></head><body>
<?php require __DIR__.'/_menu.php'; ?>
<div class="container"><h2>Gerenciar categorias</h2>
<?php if($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
<?php if(isset($_GET['ok'])): ?><div class="alerta ok">Operação realizada.</div><?php endif; ?>
<div class="form-box" style="max-width:650px"><h3><?= $edit?'Editar':'Cadastrar' ?> categoria</h3>
<form method="post"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>Nome</label><input type="text" name="nome" value="<?= e($edit['nome']??'') ?>" required>
<label>Descrição</label><input type="text" name="descricao" value="<?= e($edit['descricao']??'') ?>">
<button class="btn" type="submit">Salvar</button> <a class="btn cinza" href="categorias.php">Limpar</a></form></div>
<div class="tabela-rolavel"><table><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Ações</th></tr><?php foreach($registros as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['nome']??'') ?></td><td><?= e($r['descricao']??'') ?></td><td><a class="btn azul" href="categorias.php?editar=<?= (int)$r['id'] ?>">Editar</a>
<form method="post" style="display:inline" onsubmit="return confirm('Excluir registro?')"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="acao" value="excluir"><button class="btn vermelho">Excluir</button></form>
</td></tr><?php endforeach; ?></table></div></div></body></html>