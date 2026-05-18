<?php
require_once 'config.php';

if (!isLoggedIn()) {
    exit;
}

$tema = $_POST['tema'] ?? 'light';
$_SESSION['tema'] = $tema;

$stmt = $pdo->prepare("UPDATE usuario SET tema = ? WHERE id = ?");
$stmt->execute([$tema, $_SESSION['usuario_id']]);
?>