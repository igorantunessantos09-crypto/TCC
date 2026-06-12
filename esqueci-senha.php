<?php
require_once 'php/config.php';
require_once 'php/enviar_email.php';

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id, nome FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            // Gerar código
            $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            $stmt = $pdo->prepare("UPDATE usuario SET codigo_recuperacao = ?, expiracao_codigo = ? WHERE id = ?");
            $stmt->execute([$codigo, $expiracao, $usuario['id']]);
            
            // Enviar email
            enviarLinkRecuperacao($email, $codigo);
            
            $mensagem = "Enviamos um link de recuperação para seu email!";
        } else {
            $mensagem = "Se o email existir, enviaremos um link de recuperação.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci Minha Senha - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔑</div>
            <h1 style="color: var(--text-primary);">Esqueceu a senha?</h1>
            <p style="color: var(--text-muted);">Digite seu email para receber o link de recuperação</p>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="alert-success" style="margin-bottom: 1rem;"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email cadastrado</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <button type="submit" class="btn-primary">Enviar Link de Recuperação</button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">← Voltar ao login</a>
        </div>
    </div>
</body>
</html>