<?php
require_once '../php/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Estatísticas
$totalClientes = $pdo->query("SELECT COUNT(*) FROM usuario WHERE nivel_acesso = 'cliente'")->fetchColumn();
$totalPedidos = $pdo->query("SELECT COUNT(*) FROM pedido")->fetchColumn();
$totalVendas = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedido WHERE status = 'pago'")->fetchColumn();
$pedidosPendentes = $pdo->query("SELECT COUNT(*) FROM pedido WHERE status = 'pendente'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - FlowMonitor</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Admin -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">💧</div>
                <h2>FlowMonitor</h2>
            </div>
            <nav class="admin-nav">
                <a href="index.php" class="active">📊 Dashboard</a>
                <a href="pedidos.php">📦 Pedidos</a>
                <a href="clientes.php">👥 Clientes</a>
                <a href="produtos.php">🛍️ Produtos</a>
                <a href="../index.php">🏠 Ver Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <a href="../php/logout.php" class="btn-logout">Sair</a>
            </header>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo $totalClientes; ?></h3>
                        <p>Total de Clientes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3><?php echo $totalPedidos; ?></h3>
                        <p>Total de Pedidos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>R$ <?php echo number_format($totalVendas, 2, ',', '.'); ?></h3>
                        <p>Total em Vendas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $pedidosPendentes; ?></h3>
                        <p>Pedidos Pendentes</p>
                    </div>
                </div>
            </div>
            
            <!-- Últimos Pedidos -->
            <div class="admin-card">
                <h2>Últimos Pedidos</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pedidos = $pdo->query("
                            SELECT p.*, u.nome as cliente_nome 
                            FROM pedido p 
                            JOIN usuario u ON p.usuario_id = u.id 
                            ORDER BY p.data_pedido DESC 
                            LIMIT 10
                        ")->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($pedidos as $pedido):
                        ?>
                        <tr>
                            <td>#<?php echo $pedido['id']; ?></td>
                            <td><?php echo $pedido['cliente_nome']; ?></td>
                            <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                    <?php echo ucfirst($pedido['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>