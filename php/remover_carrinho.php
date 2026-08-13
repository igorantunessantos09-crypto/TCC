<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login']);
    exit;
}

$item_id = $_POST['item_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$item_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Remover item
$stmt = $pdo->prepare("DELETE FROM carrinho WHERE id = ? AND usuario_id = ?");
$stmt->execute([$item_id, $usuario_id]);

// Calcular totais atualizados
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(c.quantidade * p.preco), 0) as total,
           COALESCE(SUM(c.quantidade), 0) as count
    FROM carrinho c 
    JOIN produto p ON c.produto_id = p.id 
    WHERE c.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$totais = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'total' => $totais['total'],
    'count' => $totais['count']
]);
?>