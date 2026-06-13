<?php
require_once 'php/config.php';
require_once 'php/enviar_email.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // ETAPA 1: Cadastro inicial
    if ($action === 'register') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        
        // Validações
        if (empty($nome) || empty($email) || empty($senha)) {
            $erro = 'Preencha todos os campos obrigatórios.';
        } elseif (strlen($senha) < 6) {
            $erro = 'A senha deve ter no mínimo 6 caracteres.';
        } else {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id, email_verificado FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existente) {
                if ($existente['email_verificado']) {
                    $erro = 'Este email já está cadastrado.';
                } else {
                    // Reenviar código
                    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    $stmt = $pdo->prepare("UPDATE usuario SET nome = ?, senha = ?, codigo_verificacao = ?, expiracao_codigo = ? WHERE id = ?");
                    $stmt->execute([$nome, password_hash($senha, PASSWORD_DEFAULT), $codigo, $expiracao, $existente['id']]);
                    
                    $_SESSION['usuario_temp_id'] = $existente['id'];
                    $_SESSION['usuario_temp_email'] = $email;
                    
                    enviarCodigoVerificacao($email, $codigo);
                    
                    $sucesso = 'Código de verificação reenviado! Verifique seu email.';
                }
            } else {
                // Criar novo usuário
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha, telefone, nivel_acesso, codigo_verificacao, expiracao_codigo, email_verificado) VALUES (?, ?, ?, ?, 'cliente', ?, ?, FALSE)");
                $stmt->execute([$nome, $email, $senha_hash, $telefone, $codigo, $expiracao]);
                
                $_SESSION['usuario_temp_id'] = $pdo->lastInsertId();
                $_SESSION['usuario_temp_email'] = $email;
                
                // Enviar email com código
                enviarCodigoVerificacao($email, $codigo);
                
                $sucesso = 'Conta criada! Enviamos um código de verificação para seu email.';
            }
        }
    }
    
    // ETAPA 2: Verificar código
    if ($action === 'verify') {
        $codigo = trim($_POST['codigo'] ?? '');
        $usuario_id = $_SESSION['usuario_temp_id'] ?? null;
        
        if (!$usuario_id) {
            $erro = 'Sessão expirada. Faça o cadastro novamente.';
        } elseif (empty($codigo)) {
            $erro = 'Digite o código de verificação.';
        } else {
            $stmt = $pdo->prepare("SELECT id, nome, email, nivel_acesso, tema FROM usuario WHERE id = ? AND codigo_verificacao = ? AND expiracao_codigo > NOW()");
            $stmt->execute([$usuario_id, $codigo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Verificar email
                $stmt = $pdo->prepare("UPDATE usuario SET email_verificado = TRUE, codigo_verificacao = NULL, expiracao_codigo = NULL WHERE id = ?");
                $stmt->execute([$usuario_id]);
                
                // Logar automaticamente
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
                $_SESSION['tema'] = $usuario['tema'] ?? 'light';
                
                // Limpar dados temporários
                unset($_SESSION['usuario_temp_id'], $_SESSION['usuario_temp_email']);
                
                redirect('index.php?verificado=1');
            } else {
                $erro = 'Código inválido ou expirado. Solicite um novo.';
            }
        }
    }
}

// Se já tem cadastro pendente, mostrar tela de verificação
$mostrar_verificacao = isset($_SESSION['usuario_temp_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mostrar_verificacao ? 'Verificar Email' : 'Cadastro'; ?> - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .verification-container {
            max-width: 450px;
            margin: 2rem auto;
            background: var(--bg-secondary);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 25px rgba(0,0,0,0.1);
        }
        
        .code-inputs {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .code-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            transform: scale(1.05);
        }
        
        .btn-verify {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        }
        
        .btn-verify:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .timer {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        
        .timer span {
            color: var(--primary);
            font-weight: 600;
        }
        
        .alert-success {
            background: var(--success-light);
            color: #065f46;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        [data-theme="dark"] .alert-success {
            color: #34d399;
        }
        
        .alert-error {
            background: var(--danger-light);
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        [data-theme="dark"] .alert-error {
            color: #f87171;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <?php if ($mostrar_verificacao): ?>
        <!-- TELA DE VERIFICAÇÃO -->
        <div class="verification-container">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📧</div>
                <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">Verifique seu Email</h1>
                <p style="color: var(--text-muted);">
                    Enviamos um código de 6 dígitos para<br>
                    <strong style="color: var(--primary);"><?php echo $_SESSION['usuario_temp_email'] ?? ''; ?></strong>
                </p>
            </div>
            
            <?php if ($erro): ?>
                <div class="alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="verifyForm">
                <input type="hidden" name="action" value="verify">
                
                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                </div>
                
                <input type="hidden" name="codigo" id="codigoCompleto">
                
                <button type="submit" class="btn-verify" id="btnVerify">
                    ✅ Verificar Email
                </button>
            </form>
            
            <div class="timer">
                Não recebeu? 
                <a href="php/reenviar_codigo.php" style="color: var(--primary); font-weight: 600;">Reenviar código</a>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="php/cancelar_cadastro.php" style="color: var(--text-muted); font-size: 0.9rem;">
                    Cancelar cadastro
                </a>
            </div>
        </div>
        
        <script>
            // Auto-foco e navegação entre inputs
            const inputs = document.querySelectorAll('.code-input');
            
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                    atualizarCodigo();
                });
                
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
                
                // Permitir apenas números
                input.addEventListener('keypress', (e) => {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });
                
                // Colar código completo
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = pasted.replace(/\D/g, '').slice(0, 6);
                    
                    numbers.split('').forEach((num, i) => {
                        if (inputs[i]) {
                            inputs[i].value = num;
                        }
                    });
                    
                    if (inputs[numbers.length - 1]) {
                        inputs[numbers.length - 1].focus();
                    }
                    
                    atualizarCodigo();
                });
            });
            
            function atualizarCodigo() {
                const codigo = Array.from(inputs).map(i => i.value).join('');
                document.getElementById('codigoCompleto').value = codigo;
                
                // Habilitar botão quando tiver 6 dígitos
                document.getElementById('btnVerify').disabled = codigo.length !== 6;
            }
            
            // Focar no primeiro input
            inputs[0].focus();
        </script>
        
    <?php else: ?>
        <!-- TELA DE CADASTRO -->
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💧</div>
                <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">FlowMonitor</h1>
                <p style="color: var(--text-muted);">Crie sua conta gratuita</p>
            </div>
            
            <?php if ($erro): ?>
                <div class="alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label>Nome completo *</label>
                    <input type="text" name="nome" required placeholder="Seu nome completo" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required placeholder="seu@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="telefone" placeholder="(00) 00000-0000" value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Senha *</label>
                    <input type="password" name="senha" required placeholder="Mínimo 6 caracteres" minlength="6">
                </div>
                
                <button type="submit" class="btn-primary" style="margin-bottom: 1rem;">
                    Criar Conta
                </button>
            </form>
            
            <div style="text-align: center;">
                <p style="color: var(--text-muted);">
                    Já tem uma conta? 
                    <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Faça login</a>
                </p>
            </div>
            
            <a href="index.php" class="back-btn" style="display: block; text-align: center; margin-top: 1.5rem;">
                ← Voltar ao site
            </a>
        </div>
    <?php endif; ?>
</body>
</html>