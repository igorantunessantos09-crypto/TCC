<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM chat_mensagens 
    WHERE usuario_id = ? 
    ORDER BY criado_em ASC
");
$stmt->execute([$_SESSION['usuario_id']]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'mensagens' => $mensagens]);
?>