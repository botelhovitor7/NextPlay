<?php
require_once __DIR__.'/../config/sessao.php';
require_once __DIR__.'/../models/pedidos.php';
exigirAdmin('../../');
$model=new Pedido(); $erro=''; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST') {
 if(!csrfValido($_POST['token']??'')) $erro='Token inválido.';
 else {
  try {
   $id=(int)($_POST['id']??0);
   if(($_POST['acao']??'')==='excluir') { $model->excluir($id); }
   else {
    $dados=[];
    $dados['cliente_id']=trim((string)($_POST['cliente_id']??''));
    $dados['total']=trim((string)($_POST['total']??''));
    $dados['status']=trim((string)($_POST['status']??''));
    $dados['cliente_id']=$dados['cliente_id']!==''?(int)$dados['cliente_id']:null;
    if(!is_numeric($dados['total']) || (float)$dados['total']<0 || !in_array($dados['status'],['pendente','pago','cancelado'],true)) throw new RuntimeException('Dados inválidos.');
    $model->salvar($dados,$id);
   }
   header('Location: ../views/pedidos.php?ok=1'); exit;
  } catch(Throwable $e) { $erro='Não foi possível salvar: '.$e->getMessage(); }
 }
}
if(isset($_GET['editar'])) $edit=$model->buscar((int)$_GET['editar']);
$registros=$model->listar();
