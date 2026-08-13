<?php
require_once '../php/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Processar atualização de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE pedido SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['pedido_id']]);
    redirect('pedidos.php?atualizado=1');
}

// Buscar todos os pedidos
$pedidos = $pdo->query("
    SELECT p.*, u.nome as cliente_nome, u.email as cliente_email,
           (SELECT GROUP_CONCAT(CONCAT(pr.nome, ' (', i.quantidade, 'x)') SEPARATOR ', ')
            FROM item i 
            JOIN produto pr ON i.produto_id = pr.id 
            WHERE i.pedido_id = p.id) as produtos
    FROM pedido p 
    JOIN usuario u ON p.usuario_id = u.id 
    ORDER BY p.data_pedido DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Admin FlowMonitor</title>
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
                <a href="pedidos.php" class="active">📦 Pedidos</a>
                <a href="clientes.php">👥 Clientes</a>
                <a href="produtos.php">🛍️ Produtos</a>
                <a href="../index.php">🏠 Ver Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Gerenciar Pedidos</h1>
            </header>
            
            <?php if (isset($_GET['atualizado'])): ?>
                <div class="alert success">Status do pedido atualizado com sucesso!</div>
            <?php endif; ?>
            
            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Produtos</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?php echo $pedido['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($pedido['cliente_nome']); ?></strong><br>
                                <small><?php echo $pedido['cliente_email']; ?></small>
                            </td>
                            <td><?php echo $pedido['produtos']; ?></td>
                            <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" 
                                            class="status-select status-<?php echo $pedido['status']; ?>">
                                        <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="pago" <?php echo $pedido['status'] === 'pago' ? 'selected' : ''; ?>>Pago</option>
                                        <option value="enviado" <?php echo $pedido['status'] === 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                        <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
                            <td>
                                <button onclick="verDetalhes(<?php echo $pedido['id']; ?>)" class="btn-small">
                                    Detalhes
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>