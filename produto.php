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