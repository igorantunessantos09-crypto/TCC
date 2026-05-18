<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$pedidos = getPedidosUsuario($usuario_id);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Back Button -->
    <a href="index.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="account-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-photo">
                <?php echo strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="profile-info">
                <h2>Bem vindo(a) novamente</h2>
                <div class="profile-name"><?php echo htmlspecialchars($usuario['nome'] ?? 'Usuário'); ?></div>
            </div>
            <div class="logo-mini">FM</div>
        </div>
        
        <!-- Account Grid -->
        <div class="account-grid">
            <div class="account-card" onclick="window.location.href='configuracoes.php'">
                <div class="card-icon">⚙️</div>
                <div class="card-info">
                    <h3>Configurações</h3>
                    <p>Gerencie suas preferências e tema</p>
                </div>
            </div>
            
            <div class="account-card" onclick="window.location.href='pedidos.php'">
                <div class="card-icon">📦</div>
                <div class="card-info">
                    <h3>Meus Pedidos</h3>
                    <p>Acompanhe seus pedidos e planos</p>
                </div>
            </div>
            
            <div class="account-card" onclick="window.location.href='carrinho.php'">
                <div class="card-icon">🛒</div>
                <div class="card-info">
                    <h3>Carrinho</h3>
                    <p>Itens no seu carrinho de compras</p>
                </div>
            </div>
            
            <div class="account-card" style="border-color: var(--danger);" 
                 onclick="if(confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível!')) window.location.href='php/excluir_conta.php'">
                <div class="card-icon" style="background: var(--danger);">⚠️</div>
                <div class="card-info">
                    <h3 style="color: var(--danger);">Excluir Minha Conta</h3>
                    <p>Remover permanentemente sua conta</p>
                </div>
            </div>
        </div>
        
        <!-- Últimos Pedidos -->
        <?php if (!empty($pedidos)): ?>
        <div style="margin-top: 3rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--gray-900);">Últimos Pedidos</h2>
            <?php foreach (array_slice($pedidos, 0, 3) as $pedido): ?>
            <div style="background: var(--white); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1rem; box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong>Pedido #<?php echo $pedido['id']; ?></strong>
                    <span><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></span>
                </div>
                <div style="color: var(--gray-600);"><?php echo $pedido['produtos']; ?></div>
                <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                    <strong>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong>
                    <span style="color: var(--primary);"><?php echo ucfirst($pedido['status']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        const usuarioId = <?php echo $usuario_id; ?>;
    </script>
    <script src="js/script.js"></script>
</body>
</html>