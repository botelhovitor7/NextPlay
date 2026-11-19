<?php
require_once __DIR__ . '/../config/conexao.php';
class Pedido {
 private PDO $db;
 public function __construct() { global $pdo; $this->db=$pdo; }
 public function listar(): array { return $this->db->query("SELECT * FROM pedidos ORDER BY id DESC")->fetchAll(); }
 public function buscar(int $id): ?array { $s=$this->db->prepare("SELECT * FROM pedidos WHERE id=?"); $s->execute([$id]); return $s->fetch() ?: null; }
 public function salvar(array $dados, int $id=0): void {
  $campos=["cliente_id","total","status"];
  $valores=array_map(fn($c)=>$dados[$c]??null,$campos);
  if($id) { $sets=implode(',',array_map(fn($c)=>"$c=?",$campos)); $s=$this->db->prepare("UPDATE pedidos SET $sets WHERE id=?"); $valores[]=$id; }
  else { $cols=implode(',',$campos); $qs=implode(',',array_fill(0,count($campos),'?')); $s=$this->db->prepare("INSERT INTO pedidos ($cols) VALUES ($qs)"); }
  $s->execute($valores);
 }
 public function excluir(int $id): void { $s=$this->db->prepare("DELETE FROM pedidos WHERE id=?"); $s->execute([$id]); }
 public function finalizarCarrinho(int $usuarioId, array $carrinho): int {
  if(!$carrinho) throw new RuntimeException('Carrinho vazio.');
  $this->db->beginTransaction();
  try {
   $cliente=$this->db->prepare('SELECT id FROM clientes WHERE usuario_id=?'); $cliente->execute([$usuarioId]);
   $cid=$cliente->fetchColumn() ?: null;
   $itens=[]; $total=0;
   foreach($carrinho as $gid=>$q) {
    $q=(int)$q; if($q<1) throw new RuntimeException('Quantidade inválida.');
    $stmt=$this->db->prepare('SELECT id,titulo,preco,estoque FROM games WHERE id=? FOR UPDATE');
    $stmt->execute([(int)$gid]); $g=$stmt->fetch();
    if(!$g || (int)$g['estoque']<$q) throw new RuntimeException('Estoque insuficiente para finalizar.');
    $itens[]=[$g,$q]; $total+=(float)$g['preco']*$q;
   }
   $stmt=$this->db->prepare('INSERT INTO pedidos(usuario_id,cliente_id,total,status) VALUES (?,?,?,?)');
   $stmt->execute([$usuarioId,$cid,$total,'pendente']); $pedido=(int)$this->db->lastInsertId();
   $ins=$this->db->prepare('INSERT INTO pedido_itens(pedido_id,game_id,titulo,quantidade,preco_unitario) VALUES (?,?,?,?,?)');
   $stock=$this->db->prepare('UPDATE games SET estoque=estoque-? WHERE id=? AND estoque>=?');
   foreach($itens as [$g,$q]) {
    $ins->execute([$pedido,$g['id'],$g['titulo'],$q,$g['preco']]);
    $stock->execute([$q,$g['id'],$q]);
    if($stock->rowCount()!==1) throw new RuntimeException('Estoque insuficiente.');
   }
   $this->db->commit(); return $pedido;
  } catch(Throwable $e) { if($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
 }
 public function itens(int $id): array { $s=$this->db->prepare('SELECT * FROM pedido_itens WHERE pedido_id=?'); $s->execute([$id]); return $s->fetchAll(); }
}
