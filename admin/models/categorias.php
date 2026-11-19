<?php
require_once __DIR__ . '/../config/conexao.php';
class Categoria {
 private PDO $db;
 public function __construct() { global $pdo; $this->db=$pdo; }
 public function listar(): array { return $this->db->query("SELECT * FROM categorias ORDER BY id DESC")->fetchAll(); }
 public function buscar(int $id): ?array { $s=$this->db->prepare("SELECT * FROM categorias WHERE id=?"); $s->execute([$id]); return $s->fetch() ?: null; }
 public function salvar(array $dados, int $id=0): void {
  $campos=["nome","descricao"];
  $valores=array_map(fn($c)=>$dados[$c]??null,$campos);
  if($id) { $sets=implode(',',array_map(fn($c)=>"$c=?",$campos)); $s=$this->db->prepare("UPDATE categorias SET $sets WHERE id=?"); $valores[]=$id; }
  else { $cols=implode(',',$campos); $qs=implode(',',array_fill(0,count($campos),'?')); $s=$this->db->prepare("INSERT INTO categorias ($cols) VALUES ($qs)"); }
  $s->execute($valores);
 }
 public function excluir(int $id): void { $s=$this->db->prepare("DELETE FROM categorias WHERE id=?"); $s->execute([$id]); }
}
