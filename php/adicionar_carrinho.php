<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login primeiro']);
    exit;
}

$produto_id = $_POST['produto_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$produto_id) {
    echo json_encode(['success' => false, 'message' => 'Produto não especificado']);
    exit;
}

// Verificar se já está no carrinho
$stmt = $pdo->prepare("SELECT id, quantidade FROM carrinho WHERE usuario_id = ? AND produto_id = ?");
$stmt->execute([$usuario_id, $produto_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    // Atualizar quantidade
    $stmt = $pdo->prepare("UPDATE carrinho SET quantidade = quantidade + 1 WHERE id = ?");
    $stmt->execute([$item['id']]);
} else {
    // Adicionar novo item
    $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, produto_id, quantidade) VALUES (?, ?, 1)");
    $stmt->execute([$usuario_id, $produto_id]);
}

// Contar itens no carrinho
$stmt = $pdo->prepare("SELECT SUM(quantidade) as total FROM carrinho WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo json_encode(['success' => true, 'count' => $count]);
?>