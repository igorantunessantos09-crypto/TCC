<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login primeiro']);
    exit;
}

$pedido_id = $_POST['pedido_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$pedido_id) {
    echo json_encode(['success' => false, 'message' => 'Pedido não especificado']);
    exit;
}

// Verificar se o pedido pertence ao usuário
$stmt = $pdo->prepare("SELECT * FROM pedido WHERE id = ? AND usuario_id = ? AND status = 'pendente'");
$stmt->execute([$pedido_id, $usuario_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo json_encode(['success' => false, 'message' => 'Pedido não encontrado ou não pode ser cancelado']);
    exit;
}

// Cancelar pedido
$stmt = $pdo->prepare("UPDATE pedido SET status = 'cancelado' WHERE id = ?");
$stmt->execute([$pedido_id]);

// Registrar log
$stmt = $pdo->prepare("INSERT INTO logs (usuario_id, acao) VALUES (?, ?)");
$stmt->execute([$usuario_id, "Pedido #$pedido_id cancelado pelo cliente"]);

echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso']);
?>