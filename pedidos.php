<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];
$pedidos = getPedidosUsuario($usuario_id);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .pedidos-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .pedidos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .pedidos-header h1 {
            font-size: 2rem;
            color: var(--gray-900);
        }
        
        .pedidos-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-mini {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow);
        }
        
        .stat-mini .number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        .stat-mini .label {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        
        .pedido-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            transition: all 0.3s;
        }
        
        .pedido-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        
        .pedido-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .pedido-id {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1.1rem;
        }
        
        .pedido-data {
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-pendente {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-pago {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-enviado {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelado {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .pedido-produtos {
            margin-bottom: 1rem;
        }
        
        .pedido-produto-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            color: var(--gray-700);
        }
        
        .pedido-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 2px solid var(--gray-200);
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-900);
        }
        
        .pedido-total .valor {
            color: var(--primary);
            font-size: 1.25rem;
        }
        
        .pedido-acoes {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-detalhes {
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .btn-detalhes:hover {
            background: var(--primary-dark);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }
        
        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--gray-500);
            margin-bottom: 2rem;
        }
        
        /* Filtros */
        .filtros {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .filtro-btn {
            padding: 0.5rem 1.5rem;
            border: 2px solid var(--gray-300);
            background: var(--white);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            color: var(--gray-700);
        }
        
        .filtro-btn:hover,
        .filtro-btn.active {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }
        
        @media (max-width: 768px) {
            .pedidos-container {
                padding: 1rem;
            }
            
            .pedido-card-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="minha-conta.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="pedidos-container" style="margin-top: 4rem;">
        <div class="pedidos-header">
            <h1>📦 Meus Pedidos</h1>
        </div>
        
        <!-- Estatísticas -->
        <div class="pedidos-stats">
            <div class="stat-mini">
                <div class="number"><?php echo count($pedidos); ?></div>
                <div class="label">Total de Pedidos</div>
            </div>
            <div class="stat-mini">
                <div class="number">
                    R$ <?php 
                        $total_gasto = array_sum(array_column($pedidos, 'total'));
                        echo number_format($total_gasto, 2, ',', '.');
                    ?>
                </div>
                <div class="label">Total Gasto</div>
            </div>
            <div class="stat-mini">
                <div class="number">
                    <?php 
                        $pendentes = count(array_filter($pedidos, function($p) {
                            return $p['status'] === 'pendente';
                        }));
                        echo $pendentes;
                    ?>
                </div>
                <div class="label">Pedidos Pendentes</div>
            </div>
        </div>
        
        <?php if (empty($pedidos)): ?>
            <!-- Estado vazio -->
            <div class="empty-state">
                <div class="icon">📦</div>
                <h2>Nenhum pedido encontrado</h2>
                <p>Você ainda não realizou nenhuma compra. Que tal começar agora?</p>
                <a href="index.php" class="btn-plano" style="display: inline-block; text-decoration: none;">
                    Ver Planos
                </a>
            </div>
        <?php else: ?>
            <!-- Filtros -->
            <div class="filtros">
                <button class="filtro-btn active" onclick="filtrarPedidos('todos')">Todos</button>
                <button class="filtro-btn" onclick="filtrarPedidos('pendente')">Pendentes</button>
                <button class="filtro-btn" onclick="filtrarPedidos('pago')">Pagos</button>
                <button class="filtro-btn" onclick="filtrarPedidos('enviado')">Enviados</button>
                <button class="filtro-btn" onclick="filtrarPedidos('cancelado')">Cancelados</button>
            </div>
            
            <!-- Lista de Pedidos -->
            <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido-card" data-status="<?php echo $pedido['status']; ?>">
                <div class="pedido-card-header">
                    <div>
                        <span class="pedido-id">Pedido #<?php echo $pedido['id']; ?></span>
                        <span class="pedido-data" style="margin-left: 1rem;">
                            <?php echo date('d/m/Y à\s H:i', strtotime($pedido['data_pedido'])); ?>
                        </span>
                    </div>
                    <span class="status-badge status-<?php echo $pedido['status']; ?>">
                        <?php echo ucfirst($pedido['status']); ?>
                    </span>
                </div>
                
                <div class="pedido-produtos">
                    <?php 
                        $produtos_lista = explode(',', $pedido['produtos']);
                        foreach ($produtos_lista as $produto):
                    ?>
                        <div class="pedido-produto-item">
                            <span><?php echo trim($produto); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (!empty($pedido['plano'])): ?>
                    <div style="color: var(--primary); font-weight: 600; margin-bottom: 0.5rem;">
                        Plano: <?php echo ucfirst($pedido['plano']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="pedido-total">
                    <span>Total</span>
                    <span class="valor">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                </div>
                
                <div class="pedido-acoes">
                    <button class="btn-detalhes" onclick="verDetalhes(<?php echo $pedido['id']; ?>)">
                        Ver Detalhes
                    </button>
                    
                    <?php if ($pedido['status'] === 'pendente'): ?>
                        <button class="btn-detalhes" style="background: var(--danger);" 
                                onclick="if(confirm('Deseja cancelar este pedido?')) cancelarPedido(<?php echo $pedido['id']; ?>)">
                            Cancelar Pedido
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // Função para filtrar pedidos
        function filtrarPedidos(status) {
            // Atualizar botões ativos
            document.querySelectorAll('.filtro-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Filtrar cards
            const cards = document.querySelectorAll('.pedido-card');
            cards.forEach(card => {
                if (status === 'todos' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Função para ver detalhes do pedido
        function verDetalhes(pedidoId) {
            // Redirecionar para página de detalhes ou mostrar modal
            alert('Detalhes do Pedido #' + pedidoId + '\n\nFuncionalidade em desenvolvimento.');
            // Você pode criar uma página de detalhes futuramente:
            // window.location.href = 'detalhes-pedido.php?id=' + pedidoId;
        }
        
        // Função para cancelar pedido
        async function cancelarPedido(pedidoId) {
            try {
                const response = await fetch('php/cancelar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'pedido_id=' + pedidoId
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Pedido cancelado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao cancelar pedido: ' + data.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro de conexão');
            }
        }
    </script>
</body>
</html>