<?php
require_once 'config.php';

if (!isLoggedIn()) {
    exit;
}

$endereco_id = $_POST['endereco_id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];

// Remover principal de todos
$stmt = $pdo->prepare("UPDATE endereco SET tipo = 'secundario' WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);

// Definir novo principal
$stmt = $pdo->prepare("UPDATE endereco SET tipo = 'principal' WHERE id = ? AND usuario_id = ?");
$stmt->execute([$endereco_id, $usuario_id]);

echo json_encode(['success' => true]);
?>