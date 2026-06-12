<?php
require_once 'php/config.php';
require_once 'php/functions.php';

// Buscar TODOS os produtos
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
    <style>
        /* Estilos específicos da página de produtos */
        .produtos-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .produtos-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .produto-logo-small {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .produtos-header h1 {
            font-size: 2rem;
            color: var(--text-primary);
            margin: 0;
        }
        
        .produtos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
        }
        
        .produto-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .produto-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }
        
        .produto-card-imagem {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: var(--bg-tertiary);
        }
        
        .produto-card-body {
            padding: 1.5rem;
        }
        
        .produto-card-tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #10b981;
            color: white;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .produto-card-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .produto-card-body .descricao {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .produto-card-preco {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }
        
        .produto-card-preco .parcela {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            display: block;
        }
        
        .produto-card-footer {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-card-comprar {
            flex: 1;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
        }
        
        .btn-card-comprar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-card-carrinho {
            padding: 0.75rem 1rem;
            background: var(--bg-secondary);
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .btn-card-carrinho:hover {
            background: var(--primary);
            color: white;
        }
        
        @media (max-width: 768px) {
            .produtos-page {
                padding: 1rem;
            }
            
            .produtos-grid {
                grid-template-columns: 1fr;
            }
            
            .produto-card-footer {
                flex-direction: column;
            }
            
            .produtos-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
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
                <h1>Nossos Produtos</h1>
            </div>
            
            <?php if (empty($produtos)): ?>
                <div style="text-align: center; padding: 4rem; background: var(--bg-secondary); border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                    <h2 style="color: var(--text-primary);">Nenhum produto disponível</h2>
                    <p style="color: var(--text-muted); margin: 1rem 0;">Volte mais tarde para conferir nossas ofertas!</p>
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
                                    <span class="produto-card-tag" style="background: #ef4444;">❌ Indisponível</span>
                                <?php endif; ?>
                                
                                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                                <p class="descricao">
                                    <?php echo !empty($produto['descricao']) ? htmlspecialchars(substr($produto['descricao'], 0, 150)) . '...' : 'Monitoramento inteligente de fluxo de água.'; ?>
                                </p>
                                
                                <div class="produto-card-preco">
                                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                    <span class="parcela">ou 12x de R$ <?php echo number_format($produto['preco'] / 12, 2, ',', '.'); ?> sem juros</span>
                                </div>
                                
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