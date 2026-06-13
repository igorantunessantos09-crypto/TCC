<?php
require_once 'config.php';
require_once 'enviar_email.php';

if (!isset($_SESSION['usuario_temp_id'])) {
    redirect('../cadastro.php');
}

$usuario_id = $_SESSION['usuario_temp_id'];
$email = $_SESSION['usuario_temp_email'];

// Gerar novo código
$codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $pdo->prepare("UPDATE usuario SET codigo_verificacao = ?, expiracao_codigo = ? WHERE id = ?");
$stmt->execute([$codigo, $expiracao, $usuario_id]);

// Enviar email
enviarCodigoVerificacao($email, $codigo);

$_SESSION['mensagem_verificacao'] = 'Novo código enviado! Verifique seu email.';
redirect('../cadastro.php');
?>