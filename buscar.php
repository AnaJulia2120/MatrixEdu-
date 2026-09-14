<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'conexao.php';
function resposta($ok,$extra=[],$msg=''){ echo json_encode(array_merge(['ok'=>$ok,'message'=>$msg],$extra),JSON_UNESCAPED_UNICODE); exit; }
$action=$_GET['action']??'';
if($action==='dashboard'){
 $carts=[];$r=$conn->query("SELECT id,nome,total,em_uso FROM carrinhos ORDER BY id");while($x=$r->fetch_assoc())$carts[]=$x;
 $log=[];$r=$conn->query("SELECT m.sala,m.professor,m.quantidade AS qtd,m.acao,c.nome AS carrinho,DATE_FORMAT(m.data_hora,'%H:%i') AS hora FROM movimentacoes m JOIN carrinhos c ON c.id=m.carrinho_id ORDER BY m.id DESC LIMIT 30");while($x=$r->fetch_assoc())$log[]=$x;
 $user=null;if(!empty($_SESSION['user_id'])){$id=(int)$_SESSION['user_id'];$r=$conn->query("SELECT id,nome,email,tipo,licenca FROM usuarios WHERE id=$id LIMIT 1");if($r)$user=$r->fetch_assoc();if($user)$user['role']=$user['tipo'];}
 resposta(true,['carts'=>$carts,'log'=>$log,'user'=>$user]);
}
if($action==='users'){
 if(($_SESSION['role']??'')!=='admin')resposta(false,[],'Acesso restrito ao administrador.');
 $users=[];$r=$conn->query("SELECT id,nome,email,licenca FROM usuarios WHERE tipo='professor' ORDER BY id DESC");while($x=$r->fetch_assoc())$users[]=$x; resposta(true,['users'=>$users]);
}
resposta(false,[],'Ação inválida.');
?>
