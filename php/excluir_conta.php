<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Deletar dados do usuário
$pdo->beginTransaction();

try {
    // Deletar logs
    $stmt = $pdo->prepare("DELETE FROM logs WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    
    // Deletar itens dos pedidos
    $stmt = $pdo->prepare("DELETE FROM item WHERE pedido_id IN (SELECT id FROM pedido WHERE usuario_id = ?)");
    $stmt->execute([$usuario_id]);
    
    // Deletar pedidos
    $stmt = $pdo->prepare("DELETE FROM pedido WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    
    // Deletar carrinho
    $stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    
    // Deletar endereços
    $stmt = $pdo->prepare("DELETE FROM endereco WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    
    // Deletar usuário
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
    $stmt->execute([$usuario_id]);
    
    $pdo->commit();
    
    // Destruir sessão
    session_destroy();
    
    redirect('../index.php?conta_excluida=1');
} catch (Exception $e) {
    $pdo->rollBack();
    redirect('../configuracoes.php?erro=1');
}
?>