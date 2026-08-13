<?php
require_once 'php/config.php';

if (!isset($_GET['pedido_id'])) {
    redirect('index.php');
}

$tema = $_SESSION['tema'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $tema; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Confirmada - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-icon">✅</div>
        <h1 style="font-size: 2rem; color: var(--success); margin-bottom: 1rem;">Compra Realizada com Sucesso!</h1>
        <p style="font-size: 1.2rem; color: var(--gray-600); margin-bottom: 0.5rem;">
            Seu pedido #<?php echo $_GET['pedido_id']; ?> foi confirmado.
        </p>
        <p style="color: var(--gray-500); margin-bottom: 2rem;">
            Você receberá um e-mail com os detalhes da compra.
        </p>
        <a href="index.php" class="btn-plano" style="display: inline-block; text-decoration: none;">
            Voltar ao Início
        </a>
    </div>
</body>
</html>