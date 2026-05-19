<?php
require_once 'php/config.php';
require_once 'php/functions.php';

// Buscar informações do produto
$stmt = $pdo->query("SELECT * FROM produto ORDER BY criado_em DESC LIMIT 1");
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não tiver produto, usar dados padrão
if (!$produto) {
    $produto = [
        'nome' => 'Sensor FlowMonitor Pro',
        'descricao' => 'Dispositivo inteligente de monitoramento de fluxo de água em tempo real.',
        'preco' => 299.90,
        'imagem' => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500',
        'quantidade' => 50
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .produto-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .produto-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .produto-logo-small {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .produto-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 4rem;
            align-items: start;
        }
        
        .produto-imagem-container {
            position: sticky;
            top: 2rem;
        }
        
        .produto-imagem-principal {
            width: 100%;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            background: var(--white);
            padding: 2rem;
        }
        
        .produto-imagem-principal img {
            width: 100%;
            height: auto;
            border-radius: var(--radius);
        }
        
        .produto-detalhes h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .produto-tag {
            display: inline-block;
            padding: 0.35rem 1rem;
            background: var(--success);
            color: white;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .produto-preco-display {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin: 1.5rem 0;
        }
        
        .produto-preco-display .parcelas {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray-500);
            display: block;
        }
        
        .produto-descricao {
            color: var(--gray-600);
            line-height: 1.8;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            white-space: pre-line;
        }
        
        .produto-especificacoes {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow);
        }
        
        .produto-especificacoes h3 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--gray-900);
        }
        
        .especificacao-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .especificacao-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: var(--radius);
        }
        
        .especificacao-item .icon {
            font-size: 1.5rem;
        }
        
        .especificacao-item .info strong {
            display: block;
            color: var(--gray-900);
            font-size: 0.9rem;
        }
        
        .especificacao-item .info span {
            color: var(--gray-500);
            font-size: 0.85rem;
        }
        
        .produto-acoes {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-comprar-agora {
            flex: 1;
            padding: 1.2rem 2rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-comprar-agora:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-adicionar-carrinho {
            padding: 1.2rem 2rem;
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-adicionar-carrinho:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .estoque-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        .estoque-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }
        
        @media (max-width: 768px) {
            .produto-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .produto-imagem-container {
                position: static;
            }
            
            .produto-detalhes h1 {
                font-size: 2rem;
            }
            
            .especificacao-grid {
                grid-template-columns: 1fr;
            }
            
            .produto-acoes {
                flex-direction: column;
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
    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <span class="icon">🏠</span> Início
    </a></li>
    <li><a href="produto.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'produto.php' ? 'active' : ''; ?>">
        <span class="icon">📦</span> Produto
    </a></li>
    <li><a href="recursos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'recursos.php' ? 'active' : ''; ?>">
        <span class="icon">⚡</span> Recursos
    </a></li>
    <li><a href="suporte.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'suporte.php' ? 'active' : ''; ?>">
        <span class="icon">💬</span> Suporte
    </a></li>
    
    <?php if (isLoggedIn()): ?>
        <li><a href="configuracoes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : ''; ?>">
            <span class="icon">⚙️</span> Configurações
        </a></li>
        
        <!-- Link do Dashboard APENAS para admins -->
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
        
        <div class="produto-page">
            <!-- Botão Voltar -->
            <div class="produto-header">
                <a href="index.php" class="back-btn" style="margin-bottom: 0;">
                    ← Voltar
                </a>
                <div class="produto-logo-small">FM</div>
            </div>
            
            <div class="produto-grid">
                <!-- Imagem do Produto -->
                <div class="produto-imagem-container">
                    <div class="produto-imagem-principal">
                        <img src="<?php echo $produto['imagem'] ?? 'assets/produto.png'; ?>" 
                             alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    </div>
                </div>
                
                <!-- Detalhes do Produto -->
                <div class="produto-detalhes">
                    <span class="produto-tag">✅ Em estoque</span>
                    
                    <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
                    
                    <div class="produto-preco-display">
                        R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        <span class="parcelas">ou 12x de R$ <?php echo number_format($produto['preco'] / 12, 2, ',', '.'); ?> sem juros</span>
                    </div>
                    
                    <div class="produto-descricao">
                        <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                    </div>
                    
                    <!-- Especificações -->
                    <div class="produto-especificacoes">
                        <h3>Especificações Técnicas</h3>
                        <div class="especificacao-grid">
                            <div class="especificacao-item">
                                <span class="icon">📏</span>
                                <div class="info">
                                    <strong>Compatibilidade</strong>
                                    <span>Canos de 1/2" a 2"</span>
                                </div>
                            </div>
                            <div class="especificacao-item">
                                <span class="icon">🔋</span>
                                <div class="info">
                                    <strong>Bateria</strong>
                                    <span>Duração de 2 anos</span>
                                </div>
                            </div>
                            <div class="especificacao-item">
                                <span class="icon">📶</span>
                                <div class="info">
                                    <strong>Conectividade</strong>
                                    <span>WiFi 2.4GHz</span>
                                </div>
                            </div>
                            <div class="especificacao-item">
                                <span class="icon">📱</span>
                                <div class="info">
                                    <strong>App</strong>
                                    <span>iOS e Android</span>
                                </div>
                            </div>
                            <div class="especificacao-item">
                                <span class="icon">💧</span>
                                <div class="info">
                                    <strong>Precisão</strong>
                                    <span>99.8% de precisão</span>
                                </div>
                            </div>
                            <div class="especificacao-item">
                                <span class="icon">🛡️</span>
                                <div class="info">
                                    <strong>Garantia</strong>
                                    <span>2 anos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ações -->
                    <div class="produto-acoes">
                        <button class="btn-comprar-agora btn-adicionar-carrinho" data-produto="<?php echo $produto['id'] ?? 1; ?>">
                            Comprar Agora
                        </button>
                        <button class="btn-adicionar-carrinho btn-adicionar-carrinho" data-produto="<?php echo $produto['id'] ?? 1; ?>">
                            🛒 Adicionar ao Carrinho
                        </button>
                    </div>
                    
                    <div class="estoque-info">
                        <span class="estoque-dot"></span>
                        <?php echo ($produto['quantidade'] ?? 0) > 0 ? 'Em estoque - Envio imediato' : 'Fora de estoque'; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="js/script.js"></script>

    <script>
        const usuarioLogado = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
    </script>
</body>
</html>