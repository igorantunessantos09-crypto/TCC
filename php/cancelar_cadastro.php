<?php
require_once 'config.php';

if (isset($_SESSION['usuario_temp_id'])) {
    // Deletar usuário não verificado
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = ? AND email_verificado = FALSE");
    $stmt->execute([$_SESSION['usuario_temp_id']]);
    
    unset($_SESSION['usuario_temp_id'], $_SESSION['usuario_temp_email']);
}

redirect('../cadastro.php');
?>