<?php
require_once 'php/config.php';
require_once 'php/functions.php';

// Buscar TODOS os produtos (não só o primeiro)
$stmt = $pdo->query("SELECT * FROM produto ORDER BY preco ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/produto.css">
</head>
<body>
    <!-- Menu Toggle -->
    <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">💧</div>
            <h2>FlowMonitor</h2>
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="index.php"><span class="icon">🏠</span> Início</a></li>
            <li><a href="produto.php" class="active"><span class="icon">📦</span> Produto</a></li>
            <li><a href="recursos.php"><span class="icon">⚡</span> Recursos</a></li>
            <li><a href="suporte.php"><span class="icon">💬</span> Suporte</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="configuracoes.php"><span class="icon">⚙️</span> Configurações</a></li>
                <?php if (isAdmin()): ?>
                    <li style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--primary);">
                        <a href="admin/index.php" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                            <span class="icon">📊</span> Dashboard Admin
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-actions">
                <button class="cart-btn" onclick="window.location.href='carrinho.php'">
                    🛒
                    <span class="cart-count"><?php echo getCarrinhoCount(); ?></span>
                </button>
                
                <?php if (isLoggedIn()): ?>
                    <button class="user-btn" onclick="window.location.href='minha-conta.php'">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)); ?>
                        </div>
                        <span>Minha Conta</span>
                    </button>
                <?php else: ?>
                    <button class="user-btn" onclick="window.location.href='login.php'">
                        Entrar
                    </button>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="produtos-page">
            <!-- Cabeçalho -->
            <div class="produtos-header">
                <a href="index.php" class="back-btn" style="margin-bottom: 0;">
                    ← Voltar
                </a>
                <div class="produto-logo-small">FM</div>
                <h1 style="font-size: 2rem; color: var(--gray-900); margin: 0;">Nossos Produtos</h1>
            </div>
            
            <?php if (empty($produtos)): ?>
                <div style="text-align: center; padding: 4rem; background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                    <h2>Nenhum produto disponível</h2>
                    <p style="color: var(--gray-500); margin: 1rem 0;">Volte mais tarde para conferir nossas ofertas!</p>
                    <a href="index.php" class="btn-plano" style="display: inline-block; text-decoration: none;">Ver Planos</a>
                </div>
            <?php else: ?>
                <div class="produtos-grid">
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto-card">
                            <img src="<?php echo $produto['imagem'] ?: 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500'; ?>" 
                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                 class="produto-card-imagem"
                                 onerror="this.src='https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500'">
                            
                            <div class="produto-card-body">
                                <?php if ($produto['quantidade'] > 0): ?>
                                    <span class="produto-card-tag">✅ Em estoque</span>
                                <?php else: ?>
                                    <span class="produto-card-tag" style="background: var(--danger);">❌ Indisponível</span>
                                <?php endif; ?>
                                
                                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                                <p class="descricao">
                                    <?php echo !empty($produto['descricao']) ? htmlspecialchars(substr($produto['descricao'], 0, 150)) . '...' : 'Monitoramento inteligente de fluxo de água.'; ?>
                                </p>
                                
                                <div class="produto-card-preco">
                                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                    <span class="parcela">ou 12x de R$ <?php echo number_format($produto['preco'] / 12, 2, ',', '.'); ?> sem juros</span>
                                </div>
                                
                                <?php if ($produto['quantidade'] <= 5 && $produto['quantidade'] > 0): ?>
                                    <p class="estoque-baixo">⚠️ Últimas <?php echo $produto['quantidade']; ?> unidades!</p>
                                <?php endif; ?>
                                
                                <div class="produto-card-footer">
                                    <button class="btn-card-comprar btn-adicionar-carrinho" 
                                            data-produto="<?php echo $produto['id']; ?>">
                                        Comprar Agora
                                    </button>
                                    <button class="btn-card-carrinho btn-adicionar-carrinho" 
                                            data-produto="<?php echo $produto['id']; ?>">
                                        🛒
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        const usuarioLogado = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
    </script>
    <script src="js/script.js"></script>
</body>
</html>