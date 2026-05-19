<?php
require_once 'php/config.php';
require_once 'php/functions.php';

$planos = getPlanos();
$produto = getProduto(1);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlowMonitor - Monitore seu fluxo de água</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Menu Toggle Button -->
        <button class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">💧</div>
                <h2>FlowMonitor</h2>
            </div>
            
            <ul class="sidebar-nav">
                <li><a href="index.php" class="active"><span class="icon">🏠</span> Início</a></li>
                <li><a href="produto.php"><span class="icon">📦</span> Produto</a></li>
                <li><a href="recursos.php"><span class="icon">⚡</span> Recursos</a></li>
                <li><a href="suporte.php"><span class="icon">💬</span> Suporte</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="configuracoes.php"><span class="icon">⚙️</span> Configurações</a></li>
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
            
            <!-- Hero Section -->
            <section class="hero">
                <h1>Monitore seu fluxo<br>de água em tempo real</h1>
                <p>Economize água, reduza custos e proteja o meio ambiente com o FlowMonitor</p>
            </section>
            
            <!-- Carrossel de Notícias -->
            <div class="carrossel-container">
                <div class="carrossel-slides">
                    <div class="carrossel-slide">
                        <h3>🌊 Desperdício de água no Brasil</h3>
                        <p>O Brasil desperdiça cerca de 40% da água tratada. Com o FlowMonitor, você pode reduzir esse desperdício em até 70% na sua residência ou empresa.</p>
                    </div>
                    <div class="carrossel-slide">
                        <h3>💡 Economia inteligente</h3>
                        <p>Nossos usuários economizaram em média R$ 85,00 por mês na conta de água após instalar o FlowMonitor.</p>
                    </div>
                    <div class="carrossel-slide">
                        <h3>🌍 Sustentabilidade</h3>
                        <p>Cada sensor FlowMonitor instalado ajuda a preservar aproximadamente 10.000 litros de água por ano.</p>
                    </div>
                </div>
                
                <button class="carrossel-btn prev">‹</button>
                <button class="carrossel-btn next">›</button>
                
                <div class="carrossel-dots">
                    <span class="carrossel-dot active"></span>
                    <span class="carrossel-dot"></span>
                    <span class="carrossel-dot"></span>
                </div>
            </div>
            
            <!-- Por que escolher FlowMonitor -->
            <section>
                <h2 style="text-align: center; font-size: 2rem; margin-bottom: 2rem; color: var(--gray-900);">
                    Por que escolher o FlowMonitor?
                </h2>
                
                <div class="beneficios-grid">
                    <div class="beneficio-card">
                        <div class="beneficio-icon">💧</div>
                        <h3>Economia Real</h3>
                        <p>Reduza sua conta de água em até 30% com monitoramento inteligente</p>
                    </div>
                    <div class="beneficio-card">
                        <div class="beneficio-icon">📱</div>
                        <h3>App Intuitivo</h3>
                        <p>Acompanhe seu consumo em tempo real pelo smartphone</p>
                    </div>
                    <div class="beneficio-card">
                        <div class="beneficio-icon">🔔</div>
                        <h3>Alertas Inteligentes</h3>
                        <p>Receba notificações sobre vazamentos e consumo excessivo</p>
                    </div>
                    <div class="beneficio-card">
                        <div class="beneficio-icon">🌱</div>
                        <h3>Sustentabilidade</h3>
                        <p>Contribua para a preservação dos recursos hídricos</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    
            <!-- Planos -->
            <section>
                <h2 style="text-align: center; font-size: 2rem; margin-bottom: 2rem; color: var(--gray-900);">
                    Escolha seu plano
                </h2>
                
                <div class="planos-container">
                    <?php foreach ($planos as $plano): ?>
                        <div class="plano-card <?php echo $plano['tipo'] === 'premium' ? 'selected' : ''; ?>" 
                             data-plano="<?php echo $plano['tipo']; ?>">
                            <div class="plano-nome"><?php echo $plano['nome']; ?></div>
                            <div class="plano-preco">
                                R$ <?php echo number_format($plano['preco'], 2, ',', '.'); ?>
                                <span>/mês</span>
                            </div>
                            <p class="plano-descricao"><?php echo $plano['descricao']; ?></p>
                            
                            <ul class="plano-recursos">
                                <?php foreach (explode(',', $plano['recursos']) as $recurso): ?>
                                    <li><?php echo trim($recurso); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <button class="btn-plano btn-adicionar-carrinho" 
                                    data-produto="<?php echo $plano['id']; ?>">
                                Começar agora
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
    
    <script src="js/script.js"></script>
</body>
</html>