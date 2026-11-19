<?php
require_once __DIR__.'/../config/sessao.php';
require_once __DIR__.'/../models/clientes.php';
exigirAdmin('../../');
$model=new Cliente(); $erro=''; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST') {
 if(!csrfValido($_POST['token']??'')) $erro='Token inválido.';
 else {
  try {
   $id=(int)($_POST['id']??0);
   if(($_POST['acao']??'')==='excluir') { $model->excluir($id); }
   else {
    $dados=[];
    $dados['nome']=trim((string)($_POST['nome']??''));
    $dados['email']=trim((string)($_POST['email']??''));
    $dados['telefone']=trim((string)($_POST['telefone']??''));
    if(!$dados['nome'] || !filter_var($dados['email'],FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Nome e e-mail válido são obrigatórios.');
    $model->salvar($dados,$id);
   }
   header('Location: ../views/clientes.php?ok=1'); exit;
  } catch(Throwable $e) { $erro='Não foi possível salvar: '.$e->getMessage(); }
 }
}
if(isset($_GET['editar'])) $edit=$model->buscar((int)$_GET['editar']);
$registros=$model->listar();
