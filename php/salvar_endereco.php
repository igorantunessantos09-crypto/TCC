<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$usuario_id = $_SESSION['usuario_id'];
$endereco_id = $_POST['endereco_id'] ?? null;
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? '';
$bairro = $_POST['bairro'] ?? '';
$cidade = $_POST['cidade'] ?? '';
$estado = $_POST['estado'] ?? '';

// Determinar tipo (principal ou secundário)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM endereco WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$total = $stmt->fetchColumn();
$tipo = $total == 0 ? 'principal' : 'secundario';

if ($endereco_id) {
    // Atualizar
    $stmt = $pdo->prepare("UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE id=? AND usuario_id=?");
    $stmt->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco_id, $usuario_id]);
} else {
    // Inserir
    $stmt = $pdo->prepare("INSERT INTO endereco (usuario_id, cep, rua, numero, complemento, bairro, cidade, estado, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $tipo]);
}

$_SESSION['mensagem_config'] = 'Endereço salvo com sucesso! ✅';
redirect('../configuracoes.php');
?>