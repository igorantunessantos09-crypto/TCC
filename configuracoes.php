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
    
    redirect('configuracoes.php?salvo=1');
}

$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$tema = $_SESSION['tema'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $tema; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <a href="minha-conta.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="settings-container" style="margin-top: 4rem;">
        <h1 style="text-align: center; margin-bottom: 2rem; color: var(--gray-900);">Configurações</h1>
        
        <?php if (isset($_GET['salvo'])): ?>
            <div style="background: var(--success); color: white; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; text-align: center;">
                Configurações salvas com sucesso!
            </div>
        <?php endif; ?>
        
        <!-- Tema -->
        <div class="setting-card">
            <h3>Aparência</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>Tema escuro</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="theme-toggle" <?php echo ($_SESSION['tema'] ?? 'light') === 'dark' ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
        
        <!-- Editar Perfil -->
        <div class="setting-card">
            <h3>Editar Perfil</h3>
            <form method="POST" action="configuracoes.php">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nova Senha (deixe em branco para manter)</label>
                    <input type="password" name="senha" placeholder="Nova senha">
                </div>
                <button type="submit" class="btn-plano">Salvar Configurações</button>
            </form>
        </div>
    </div>
    
    <script>
        const usuarioId = <?php echo $_SESSION['usuario_id']; ?>;
    </script>
    <script src="js/script.js"></script>
</body>
</html>