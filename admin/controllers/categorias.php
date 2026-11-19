<?php
require_once __DIR__.'/../config/sessao.php';
require_once __DIR__.'/../models/categorias.php';
exigirAdmin('../../');
$model=new Categoria(); $erro=''; $edit=null;
if($_SERVER['REQUEST_METHOD']==='POST') {
 if(!csrfValido($_POST['token']??'')) $erro='Token inválido.';
 else {
  try {
   $id=(int)($_POST['id']??0);
   if(($_POST['acao']??'')==='excluir') { $model->excluir($id); }
   else {
    $dados=[];
    $dados['nome']=trim((string)($_POST['nome']??''));
    $dados['descricao']=trim((string)($_POST['descricao']??''));
    if(!$dados['nome']) throw new RuntimeException('Informe a categoria.');
    $model->salvar($dados,$id);
   }
   header('Location: ../views/categorias.php?ok=1'); exit;
  } catch(Throwable $e) { $erro='Não foi possível salvar: '.$e->getMessage(); }
 }
}
if(isset($_GET['editar'])) $edit=$model->buscar((int)$_GET['editar']);
$registros=$model->listar();
