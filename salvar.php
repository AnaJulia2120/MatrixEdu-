<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'conexao.php';
require_once 'email.php';

function resposta($ok, $message = '', $extra = []) {
    echo json_encode(array_merge(['ok'=>$ok, 'message'=>$message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
function exigirLogin($admin=false) {
    if (empty($_SESSION['user_id'])) resposta(false, 'Você precisa estar logado.');
    if ($admin && ($_SESSION['role'] ?? '') !== 'admin') resposta(false, 'Acesso restrito ao administrador.');
}
function dashboardData($conn) {
    $carts=[];
    $r=$conn->query("SELECT id,nome,total,em_uso FROM carrinhos ORDER BY id");
    while($row=$r->fetch_assoc()) $carts[]=$row;
    $log=[];
    $r=$conn->query("SELECT m.sala,m.professor,m.quantidade AS qtd,m.acao AS acao,c.nome AS carrinho,DATE_FORMAT(m.data_hora,'%H:%i') AS hora FROM movimentacoes m JOIN carrinhos c ON c.id=m.carrinho_id ORDER BY m.id DESC LIMIT 30");
    while($row=$r->fetch_assoc()) $log[]=$row;
    return [$carts,$log];
}
$acao=$_POST['acao'] ?? '';

if($acao==='cadastro') {
    $nome=trim($_POST['nome']??''); $email=strtolower(trim($_POST['email']??'')); $senha=$_POST['senha']??'';
    if(!$nome || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($senha)<4) resposta(false,'Dados inválidos.');
    $hash=password_hash($senha,PASSWORD_DEFAULT);
    $stmt=$conn->prepare("INSERT INTO usuarios (nome,email,senha,tipo,licenca) VALUES (?,?,?,'professor','pendente')");
    $stmt->bind_param('sss',$nome,$email,$hash);
    if(!$stmt->execute()) resposta(false, $conn->errno===1062 ? 'Esse e-mail já está cadastrado.' : 'Erro ao cadastrar.');
    resposta(true,'Conta criada com sucesso.');
}

if($acao==='login') {
    $email=strtolower(trim($_POST['email']??'')); $senha=$_POST['senha']??'';
    $stmt=$conn->prepare("SELECT id,nome,email,senha,tipo,licenca FROM usuarios WHERE email=? LIMIT 1"); $stmt->bind_param('s',$email); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
    if(!$u || !password_verify($senha,$u['senha'])) resposta(false,'E-mail ou senha incorretos.');
    if($u['tipo']==='professor' && $u['licenca']!=='liberada') resposta(false,$u['licenca']==='pendente'?'Sua licença ainda não foi liberada pelo administrador.':'Sua licença foi bloqueada. Fale com o administrador.');
    $_SESSION['user_id']=$u['id']; $_SESSION['role']=$u['tipo'];
    unset($u['senha']); $u['role']=$u['tipo'];
    resposta(true,'Login realizado.',['user'=>$u]);
}

if($acao==='logout') { session_destroy(); resposta(true,'Saiu.'); }

if($acao==='licenca') {
    exigirLogin(true);

    $id=(int)($_POST['user_id']??0);
    $status=$_POST['status']??'';

    if(!in_array($status,['liberada','bloqueada'],true)) {
        resposta(false,'Status inválido.');
    }

    // Busca os dados do professor antes da alteração para poder enviar o aviso.
    $stmt=$conn->prepare("SELECT nome,email,licenca FROM usuarios WHERE id=? AND tipo='professor' LIMIT 1");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $professor=$stmt->get_result()->fetch_assoc();

    if(!$professor) {
        resposta(false,'Professor não encontrado.');
    }

    // Evita enviar e-mail quando o administrador clica no mesmo status novamente.
    if($professor['licenca'] === $status) {
        resposta(true,'A licença já está com esse status.');
    }

    $stmt=$conn->prepare("UPDATE usuarios SET licenca=? WHERE id=? AND tipo='professor'");
    $stmt->bind_param('si',$status,$id);

    if(!$stmt->execute()) {
        resposta(false,'Não foi possível atualizar a licença.');
    }

    // O e-mail não impede a alteração da licença.
    // Se o SMTP estiver configurado corretamente, o professor recebe o aviso.
    $emailEnviado = matrixedu_enviar_email_licenca(
        $professor['nome'],
        $professor['email'],
        $status
    );

    resposta(
        true,
        $emailEnviado
            ? 'Licença atualizada e e-mail enviado.'
            : 'Licença atualizada. O e-mail não pôde ser enviado; verifique a configuração SMTP.'
    );
}

if($acao==='movimentacao') {
    exigirLogin();
    $carrinho=(int)($_POST['carrinho_id']??0); $sala=trim($_POST['sala']??''); $prof=trim($_POST['professor']??''); $qtd=(int)($_POST['quantidade']??0); $tipo=$_POST['tipo_movimentacao']??'';
    if($carrinho<1 || !$sala || $qtd<1 || !in_array($tipo,['retirada','devolucao'],true)) resposta(false,'Preencha os dados corretamente.');
    $conn->begin_transaction();
    try {
        $stmt=$conn->prepare("SELECT total,em_uso,nome FROM carrinhos WHERE id=? FOR UPDATE"); $stmt->bind_param('i',$carrinho); $stmt->execute(); $c=$stmt->get_result()->fetch_assoc();
        if(!$c) throw new Exception('Carrinho não encontrado.');
        if($tipo==='retirada') { $disponiveis=$c['total']-$c['em_uso']; if($qtd>$disponiveis) throw new Exception("Só há {$disponiveis} equipamentos disponíveis nesse carrinho."); $novo=$c['em_uso']+$qtd; }
        else { if($qtd>$c['em_uso']) throw new Exception("Esse carrinho tem apenas {$c['em_uso']} equipamentos em uso para devolver."); $novo=$c['em_uso']-$qtd; }
        $stmt=$conn->prepare("UPDATE carrinhos SET em_uso=? WHERE id=?"); $stmt->bind_param('ii',$novo,$carrinho); $stmt->execute();
        $stmt=$conn->prepare("INSERT INTO movimentacoes(carrinho_id,sala,professor,quantidade,acao) VALUES(?,?,?,?,?)"); $stmt->bind_param('issis',$carrinho,$sala,$prof,$qtd,$tipo); $stmt->execute();
        $conn->commit(); [$carts,$log]=dashboardData($conn); resposta(true,'Movimentação salva.',['carts'=>$carts,'log'=>$log]);
    } catch(Exception $e) { $conn->rollback(); resposta(false,$e->getMessage()); }
}
resposta(false,'Ação inválida.');
?>
