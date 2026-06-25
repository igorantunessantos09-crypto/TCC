<?php
require_once 'config.php';

// Salvar tema atual antes de destruir
$tema_atual = $_SESSION['tema'] ?? 'light';

// Destruir sessão
session_destroy();

// Iniciar nova sessão limpa
session_start();

// NÃO manter o tema - vai para o padrão claro
$_SESSION['tema'] = 'light';

redirect('../index.php');
?>