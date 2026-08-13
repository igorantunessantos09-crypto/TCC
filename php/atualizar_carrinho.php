<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login']);
    exit;
}

$item_id = $_POST['item_id'] ?? null;
$quantidade = $_POST['quantidade'] ?? 1;
$usuario_id = $_SESSION['usuario_id'];

if (!$item_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Atualizar quantidade
$stmt = $pdo->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ? AND usuario_id = ?");
$stmt->execute([$quantidade, $item_id, $usuario_id]);

// Calcular totais
$stmt = $pdo->prepare("
    SELECT SUM(c.quantidade * p.preco) as total,
           SUM(c.quantidade) as count
    FROM carrinho c 
    JOIN produto p ON c.produto_id = p.id 
    WHERE c.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$totais = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar itens atualizados
$stmt = $pdo->prepare("
    SELECT c.id, c.quantidade, p.preco,
           (c.quantidade * p.preco) as subtotal
    FROM carrinho c 
    JOIN produto p ON c.produto_id = p.id 
    WHERE c.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'total' => $totais['total'] ?? 0,
    'count' => $totais['count'] ?? 0,
    'itens' => $itens
]);
?>