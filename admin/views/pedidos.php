<?php require_once __DIR__.'/../controllers/pedidos.php'; ?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pedidos — NextPlay</title><link rel="stylesheet" href="../public/css/style.css"></head><body>
<?php require __DIR__.'/_menu.php'; ?>
<div class="container"><h2>Gerenciar pedidos</h2>
<?php if($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
<?php if(isset($_GET['ok'])): ?><div class="alerta ok">Operação realizada.</div><?php endif; ?>
<div class="form-box" style="max-width:650px"><h3><?= $edit?'Editar':'Cadastrar' ?> pedido</h3>
<form method="post"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>ID do cliente (opcional)</label><input type="number" name="cliente_id" value="<?= e($edit['cliente_id']??'') ?>">
<label>Total (R$)</label><input type="number" name="total" value="<?= e($edit['total']??'') ?>" step="0.01" min="0" required>
<label>Status</label><select name="status"><?php foreach(['pendente','pago','cancelado'] as $s): ?><option value="<?= e($s) ?>" <?= ($edit['status']??'')===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?></select>
<button class="btn" type="submit">Salvar</button> <a class="btn cinza" href="pedidos.php">Limpar</a></form></div>
<div class="tabela-rolavel"><table><tr><th>ID</th><th>ID do cliente (opcional)</th><th>Total (R$)</th><th>Status</th><th>Ações</th></tr><?php foreach($registros as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['cliente_id']??'') ?></td><td><?= e($r['total']??'') ?></td><td><?= e($r['status']??'') ?></td><td><a class="btn azul" href="pedidos.php?editar=<?= (int)$r['id'] ?>">Editar</a>
<form method="post" style="display:inline" onsubmit="return confirm('Excluir registro?')"><input type="hidden" name="token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="acao" value="excluir"><button class="btn vermelho">Excluir</button></form>
<details><summary>Itens</summary><?php foreach($model->itens((int)$r['id']) as $item): ?><p><?= e($item['titulo']) ?> × <?= (int)$item['quantidade'] ?> — <?= moeda($item['preco_unitario']) ?></p><?php endforeach; ?></details></td></tr><?php endforeach; ?></table></div></div></body></html>