<?php
require_once 'php/config.php';

if (isLoggedIn()) {
    redirect('minha-conta.php');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="form-container" style="max-width: 450px; width: 100%;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">💧</div>
            <h1 style="color: var(--gray-900); margin-bottom: 0.5rem;">FlowMonitor</h1>
            <p style="color: var(--gray-500);">Crie sua conta</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: var(--danger); color: white; padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem;">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="php/auth.php" method="POST">
            <input type="hidden" name="action" value="register">
            
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="nome" required placeholder="Seu nome">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-group">
                <label>Telefone</label>
                <input type="tel" name="telefone" placeholder="(00) 00000-0000">
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required placeholder="Mínimo 6 caracteres" minlength="6">
            </div>
            
            <button type="submit" class="btn-primary" style="margin-bottom: 1rem;">
                Criar Conta
            </button>
        </form>
        
        <div style="text-align: center;">
            <p style="color: var(--gray-500);">
                Já tem uma conta? <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Faça login</a>
            </p>
        </div>
        
        <a href="index.php" class="back-btn" style="display: block; text-align: center; margin-top: 1.5rem;">
            ← Voltar ao site
        </a>
    </div>
</body>
</html>