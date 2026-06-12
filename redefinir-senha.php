<?php
require_once 'php/config.php';

$codigo = $_GET['codigo'] ?? '';
$email = $_GET['email'] ?? '';
$erro = '';
$sucesso = '';

// Verificar código
$stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ? AND codigo_recuperacao = ? AND expiracao_codigo > NOW()");
$stmt->execute([$email, $codigo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $erro = 'Link inválido ou expirado. Solicite novamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
    $nova_senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (strlen($nova_senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif ($nova_senha !== $confirmar_senha) {
        $erro = 'As senhas não conferem.';
    } else {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuario SET senha = ?, codigo_recuperacao = NULL, expiracao_codigo = NULL WHERE id = ?");
        $stmt->execute([$senha_hash, $usuario['id']]);
        
        $sucesso = 'Senha redefinida com sucesso! Redirecionando para o login...';
        header("Refresh: 3; url=login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">Redefinir Senha</h1>
        
        <?php if ($erro): ?>
            <div style="background: var(--danger-light); color: var(--danger); padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert-success" style="margin-bottom: 1rem;"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <?php if ($usuario && !$sucesso): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="senha" required placeholder="Mínimo 6 caracteres" minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" required placeholder="Repita a senha">
                </div>
                <button type="submit" class="btn-primary">Redefinir Senha</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>