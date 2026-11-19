<?php require_once __DIR__.'/../controllers/clientes.php'; ?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Clientes — NextPlay</title><link rel="stylesheet" href="../public/css/style.css"></head><body>
<?php require __DIR__.'/_menu.php'; ?>
<div class="container"><h2>Gerenciar clientes</h2>
<?php if($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
<?php if(isset($_GET['ok'])): ?><div class="alerta ok">Operação realizada.</div><?php endif; ?>
<div class="form-box" style="max-width:650px"><h3><?= $edit?'Editar':'Cadastrar' ?> cliente</h3>
<form method="post"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>Nome</label><input type="text" name="nome" value="<?= e($edit['nome']??'') ?>" required>
<label>E-mail</label><input type="email" name="email" value="<?= e($edit['email']??'') ?>" required>
<label>Telefone</label><input type="text" name="telefone" value="<?= e($edit['telefone']??'') ?>">
<button class="btn" type="submit">Salvar</button> <a class="btn cinza" href="clientes.php">Limpar</a></form></div>
<div class="tabela-rolavel"><table><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr><?php foreach($registros as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['nome']??'') ?></td><td><?= e($r['email']??'') ?></td><td><?= e($r['telefone']??'') ?></td><td><a class="btn azul" href="clientes.php?editar=<?= (int)$r['id'] ?>">Editar</a>
<form method="post" style="display:inline" onsubmit="return confirm('Excluir registro?')"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="acao" value="excluir"><button class="btn vermelho">Excluir</button></form>
</td></tr><?php endforeach; ?></table></div></div></body></html>