<?php
require_once '../php/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$clientes = $pdo->query("
    SELECT u.*, 
           COUNT(p.id) as total_pedidos,
           COALESCE(SUM(p.total), 0) as total_gasto
    FROM usuario u 
    LEFT JOIN pedido p ON u.id = p.usuario_id 
    WHERE u.nivel_acesso = 'cliente'
    GROUP BY u.id
    ORDER BY u.criado_em DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Admin FlowMonitor</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">💧</div>
                <h2>FlowMonitor</h2>
            </div>
            <nav class="admin-nav">
                <a href="index.php">📊 Dashboard</a>
                <a href="pedidos.php">📦 Pedidos</a>
                <a href="clientes.php" class="active">👥 Clientes</a>
                <a href="produtos.php">🛍️ Produtos</a>
                <a href="../index.php">🏠 Ver Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Clientes</h1>
            </header>
            
            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Total Pedidos</th>
                            <th>Total Gasto</th>
                            <th>Data Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td>#<?php echo $cliente['id']; ?></td>
                            <td><?php echo $cliente['nome'] ?? 'N/A'; ?></td>
                            <td><?php echo $cliente['email']; ?></td>
                            <td><?php echo $cliente['telefone'] ?? 'N/A'; ?></td>
                            <td><?php echo $cliente['total_pedidos']; ?></td>
                            <td>R$ <?php echo number_format($cliente['total_gasto'], 2, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($cliente['criado_em'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>