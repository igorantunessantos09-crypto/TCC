<?php
require_once 'php/config.php';
require_once 'php/functions.php';
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .recursos-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .recursos-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .recursos-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }
        
        .recursos-header p {
            font-size: 1.2rem;
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .recursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .recurso-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .recurso-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }
        
        .recurso-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .recurso-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }
        
        .recurso-card p {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .recurso-features {
            list-style: none;
        }
        
        .recurso-features li {
            padding: 0.5rem 0;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .recurso-features li::before {
            content: '✓';
            color: var(--success);
            font-weight: bold;
        }
        
        .cta-section {
            text-align: center;
            margin-top: 4rem;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-lg);
            color: white;
        }
        
        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        @media (max-width: 768px) {
            .recursos-header h1 {
                font-size: 2rem;
            }
            
            .recursos-grid {
                grid-template-columns: 1fr;
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
            <li><a href="produto.php"><span class="icon">📦</span> Produto</a></li>
            <li><a href="recursos.php" class="active"><span class="icon">⚡</span> Recursos</a></li>
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
        
        <div class="recursos-page">
            <a href="index.php" class="back-btn">← Voltar</a>
            
            <div class="recursos-header">
                <h1>Recursos do FlowMonitor</h1>
                <p>Descubra todos os recursos que fazem do FlowMonitor a melhor escolha para monitoramento de água</p>
            </div>
            
            <div class="recursos-grid">
                <!-- Recurso 1 -->
                <div class="recurso-card">
                    <div class="recurso-icon">📊</div>
                    <h3>Monitoramento em Tempo Real</h3>
                    <p>Acompanhe o fluxo de água da sua residência ou empresa em tempo real pelo aplicativo.</p>
                    <ul class="recurso-features">
                        <li>Dados atualizados a cada segundo</li>
                        <li>Gráficos interativos</li>
                        <li>Histórico completo</li>
                        <li>Exportação de dados</li>
                    </ul>
                </div>
                
                <!-- Recurso 2 -->
                <div class="recurso-card">
                    <div class="recurso-icon">🔔</div>
                    <h3>Alertas Inteligentes</h3>
                    <p>Receba notificações instantâneas sobre vazamentos, consumo excessivo e anomalias.</p>
                    <ul class="recurso-features">
                        <li>Notificações push</li>
                        <li>Alertas por e-mail</li>
                        <li>SMS para emergências</li>
                        <li>Personalização de limites</li>
                    </ul>
                </div>
                
                <!-- Recurso 3 -->
                <div class="recurso-card">
                    <div class="recurso-icon">📈</div>
                    <h3>Relatórios Detalhados</h3>
                    <p>Gere relatórios completos sobre seu consumo de água com análises e insights.</p>
                    <ul class="recurso-features">
                        <li>Relatórios diários</li>
                        <li>Comparativos mensais</li>
                        <li>Previsão de consumo</li>
                        <li>Dicas de economia</li>
                    </ul>
                </div>
                
                <!-- Recurso 4 -->
                <div class="recurso-card">
                    <div class="recurso-icon">🔧</div>
                    <h3>Instalação Simples</h3>
                    <p>Instale você mesmo em menos de 5 minutos, sem necessidade de encanador.</p>
                    <ul class="recurso-features">
                        <li>Sem ferramentas especiais</li>
                        <li>Compatível com qualquer cano</li>
                        <li>Manual ilustrado</li>
                        <li>Suporte por vídeo</li>
                    </ul>
                </div>
                
                <!-- Recurso 5 -->
                <div class="recurso-card">
                    <div class="recurso-icon">💰</div>
                    <h3>Economia Garantida</h3>
                    <p>Nossos usuários economizam em média 30% na conta de água após a instalação.</p>
                    <ul class="recurso-features">
                        <li>Detecção de vazamentos</li>
                        <li>Otimização de consumo</li>
                        <li>ROI em 3 meses</li>
                        <li>Relatório de economia</li>
                    </ul>
                </div>
                
                <!-- Recurso 6 -->
                <div class="recurso-card">
                    <div class="recurso-icon">🔒</div>
                    <h3>Dados Seguros</h3>
                    <p>Seus dados são criptografados e armazenados com segurança na nuvem.</p>
                    <ul class="recurso-features">
                        <li>Criptografia ponta a ponta</li>
                        <li>Backup automático</li>
                        <li>Conformidade LGPD</li>
                        <li>Acesso restrito</li>
                    </ul>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="cta-section">
                <h2>Pronto para economizar água?</h2>
                <p>Escolha o plano ideal para você e comece a monitorar seu consumo hoje mesmo</p>
                <button class="btn-plano" onclick="window.location.href='index.php#planos'" 
                        style="background: white; color: var(--primary); font-size: 1.2rem; padding: 1rem 3rem; display: inline-block;">
                    Ver Planos
                </button>
            </div>
        </div>
    </main>
    
    <script src="js/script.js"></script>
</body>
</html>