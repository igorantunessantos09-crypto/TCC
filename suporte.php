<?php
require_once 'php/config.php';
require_once 'php/functions.php';

// Processar envio de mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    
    $mensagem = trim($_POST['mensagem']);
    $usuario_id = $_SESSION['usuario_id'];
    
    if (!empty($mensagem)) {
        $stmt = $pdo->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, enviado_por) VALUES (?, ?, 'cliente')");
        $stmt->execute([$usuario_id, $mensagem]);
    }
    
    // Redirecionar para evitar reenvio
    redirect('suporte.php?enviado=1');
}

// Buscar mensagens do usuário logado
$mensagens = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.nome as nome_usuario 
        FROM chat_mensagens m 
        JOIN usuario u ON m.usuario_id = u.id 
        WHERE m.usuario_id = ? 
        ORDER BY m.criado_em ASC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar admin online (para mostrar status)
$admin_online = $pdo->query("SELECT COUNT(*) FROM usuario WHERE nivel_acesso = 'admin'")->fetchColumn() > 0;
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .suporte-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .suporte-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .chat-container {
            background: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 500px;
            border: 1px solid var(--border-color);
        }
        
        .chat-header {
            padding: 1.5rem;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-header h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-primary);
            font-size: 1.1rem;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }
        
        .status-dot.offline {
            background: var(--text-muted);
            animation: none;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .chat-message {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 16px;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chat-message.cliente {
            align-self: flex-end;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .chat-message.admin {
            align-self: flex-start;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border-color);
        }
        
        .chat-message .msg-info {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
            opacity: 0.8;
        }
        
        .chat-message .msg-text {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .chat-input-area {
            padding: 1.5rem;
            border-top: 2px solid var(--border-color);
            display: flex;
            gap: 0.75rem;
        }
        
        .chat-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 25px;
            font-size: 0.95rem;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-family: inherit;
            resize: none;
            transition: all 0.3s ease;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .btn-enviar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.25rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-enviar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        .btn-enviar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .redes-sociais {
            display: grid;
            gap: 1rem;
        }
        
        .rede-social-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .rede-social-card:hover {
            border-color: var(--primary);
            transform: translateX(6px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .rede-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }
        
        .rede-icon.tiktok { background: #000; }
        .rede-icon.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .rede-icon.youtube { background: #ff0000; }
        .rede-icon.app { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        
        .rede-info h4 {
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .rede-info p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .login-prompt {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .login-prompt a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-prompt a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .suporte-grid {
                grid-template-columns: 1fr;
            }
            
            .suporte-page {
                padding: 1rem;
            }
            
            .chat-container {
                height: 400px;
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
            <li><a href="recursos.php"><span class="icon">⚡</span> Recursos</a></li>
            <li><a href="suporte.php" class="active"><span class="icon">💬</span> Suporte</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="minha-conta.php"><span class="icon">👤</span> Minha Conta</a></li>
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
        
        <div class="suporte-page">
            <a href="index.php" class="back-btn" style="margin-bottom: 2rem;">← Voltar</a>
            
            <h1 style="font-size: 2rem; margin-bottom: 2rem; color: var(--text-primary);">💬 Central de Suporte</h1>
            
            <div class="suporte-grid">
                <!-- Chat -->
                <div>
                    <h2 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.3rem;">Chat ao Vivo</h2>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="chat-container">
                            <div class="chat-header">
                                <h3>
                                    <span class="status-dot <?php echo $admin_online ? '' : 'offline'; ?>"></span>
                                    <?php echo $admin_online ? 'Equipe Online' : 'Equipe Offline'; ?>
                                </h3>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?php echo $admin_online ? 'Resposta em até 5 min' : 'Responderemos em breve'; ?>
                                </span>
                            </div>
                            
                            <div class="chat-messages" id="chatMessages">
                                <?php if (empty($mensagens)): ?>
                                    <div style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
                                        <p>Envie uma mensagem para iniciar o atendimento</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($mensagens as $msg): ?>
                                        <div class="chat-message <?php echo $msg['enviado_por']; ?>">
                                            <div class="msg-info">
                                                <?php echo $msg['enviado_por'] === 'cliente' ? 'Você' : 'Suporte FlowMonitor'; ?> 
                                                • <?php echo date('H:i', strtotime($msg['criado_em'])); ?>
                                            </div>
                                            <div class="msg-text"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <form method="POST" class="chat-input-area" id="chatForm">
                                <input type="text" 
                                       name="mensagem" 
                                       class="chat-input" 
                                       placeholder="Digite sua mensagem..."
                                       required
                                       autocomplete="off">
                                <button type="submit" class="btn-enviar" id="btnEnviar">➤</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="chat-container">
                            <div class="login-prompt" style="padding-top: 5rem;">
                                <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
                                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Faça login para usar o chat</h3>
                                <p>
                                    <a href="login.php">Entrar</a> ou 
                                    <a href="cadastro.php">Criar conta</a>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Redes Sociais -->
                <div>
                    <h2 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.3rem;">Nossas Redes</h2>
                    
                    <div class="redes-sociais">
                        <a href="https://tiktok.com/@flowmonitor" target="_blank" class="rede-social-card">
                            <div class="rede-icon tiktok">🎵</div>
                            <div class="rede-info">
                                <h4>TikTok</h4>
                                <p>@flowmonitor</p>
                            </div>
                        </a>
                        
                        <a href="https://instagram.com/flowmonitor" target="_blank" class="rede-social-card">
                            <div class="rede-icon instagram">📸</div>
                            <div class="rede-info">
                                <h4>Instagram</h4>
                                <p>@flowmonitor</p>
                            </div>
                        </a>
                        
                        <a href="https://youtube.com/@flowmonitor" target="_blank" class="rede-social-card">
                            <div class="rede-icon youtube">▶️</div>
                            <div class="rede-info">
                                <h4>YouTube</h4>
                                <p>FlowMonitor Oficial</p>
                            </div>
                        </a>
                        
                        <div class="rede-social-card">
                            <div class="rede-icon app">📱</div>
                            <div class="rede-info">
                                <h4>App FlowMonitor</h4>
                                <p>Disponível para iOS e Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        const usuarioLogado = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        
        // Scroll automático para última mensagem
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Auto-refresh do chat a cada 10 segundos (se logado)
        <?php if (isLoggedIn()): ?>
        setInterval(() => {
            fetch('php/buscar_mensagens.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.mensagens) {
                        // Atualizar mensagens se houver novas
                        const currentCount = document.querySelectorAll('.chat-message').length;
                        if (data.mensagens.length > currentCount) {
                            location.reload();
                        }
                    }
                })
                .catch(() => {});
        }, 10000);
        <?php endif; ?>
    </script>
    <script src="js/script.js"></script>
</body>
</html>