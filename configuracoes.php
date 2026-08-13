<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE usuario SET nome = ?, email = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $_SESSION['usuario_id']]);
    
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuario SET senha = ? WHERE id = ?");
        $stmt->execute([$senha_hash, $_SESSION['usuario_id']]);
    }
    
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['mensagem_config'] = 'Configurações salvas com sucesso! ✅';
    
    redirect('configuracoes.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$mensagem = $_SESSION['mensagem_config'] ?? null;
unset($_SESSION['mensagem_config']);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/config.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <li><a href="suporte.php"><span class="icon">💬</span> Suporte</a></li>
            <li><a href="minha-conta.php"><span class="icon">👤</span> Minha Conta</a></li>
            <li><a href="pedidos.php"><span class="icon">📋</span> Meus Pedidos</a></li>
            <li><a href="configuracoes.php" class="active"><span class="icon">⚙️</span> Configurações</a></li>
            
            <?php if (isAdmin()): ?>
                <li style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--primary);">
                    <a href="admin/index.php" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                        <span class="icon">📊</span> Dashboard Admin
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="settings-page">
            <!-- Cabeçalho -->
            <div class="settings-header">
                <a href="minha-conta.php" class="back-btn" style="margin-bottom: 1.5rem;">
                    ← Voltar
                </a>
                <h1>⚙️ Configurações</h1>
                <p>Personalize sua experiência no FlowMonitor</p>
            </div>
            
            <?php if ($mensagem): ?>
                <div class="alert-success">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Tema -->
            <div class="setting-card">
                <h3>
                    <span class="icon">🎨</span> Aparência
                </h3>
                <div class="toggle-row">
                    <div class="toggle-label">
                        <span>Modo Escuro</span>
                        <span>Ative para reduzir o cansaço visual</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="theme-toggle" <?php echo ($_SESSION['tema'] ?? 'light') === 'dark' ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            
            <!-- Editar Perfil -->
            <div class="setting-card">
                <h3>
                    <span class="icon">👤</span> Informações Pessoais
                </h3>
                <form method="POST" action="configuracoes.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome completo</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" 
                                   required placeholder="Seu nome">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" 
                                   required placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="password" name="senha" placeholder="Deixe em branco para manter a atual" minlength="6">
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Mínimo de 6 caracteres
                        </span>
                    </div>
                    <button type="submit" class="btn-save">
                        💾 Salvar Alterações
                    </button>
                </form>
            </div>
            
            <!-- Informações da Conta -->
            <div class="setting-card">
                <h3>
                    <span class="icon">ℹ️</span> Informações da Conta
                </h3>
                <div style="color: var(--text-secondary); line-height: 2;">
                    <div><strong>Tipo de conta:</strong> <?php echo ucfirst($usuario['nivel_acesso'] ?? 'Cliente'); ?></div>
                    <div><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($usuario['criado_em'] ?? 'now')); ?></div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        const usuarioLogado = true;
        const usuarioId = <?php echo $_SESSION['usuario_id']; ?>;
    </script>
    <script src="js/script.js"></script>
</body>
</html>