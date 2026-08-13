<?php
require_once 'config.php';

// Processar login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    csrfVerify();

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Previne session fixation: troca o ID da sessão após autenticar
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
        $_SESSION['tema'] = $usuario['tema'] ?? 'light';
        
        if ($usuario['nivel_acesso'] === 'admin') {
            redirect('../admin/index.php');
        } else {
            redirect('../index.php');
        }
    } else {
        $_SESSION['error'] = 'Email ou senha incorretos';
        redirect('../login.php');
    }
}

// Processar registro
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    csrfVerify();

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = $_POST['telefone'] ?? null;

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($_POST['senha'] ?? '') < 6) {
        $_SESSION['error'] = 'Preencha os dados corretamente (senha com no mínimo 6 caracteres)';
        redirect('../cadastro.php');
    }

    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Este email já está cadastrado';
        redirect('../cadastro.php');
    }
    
    $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha, telefone, nivel_acesso) VALUES (?, ?, ?, ?, 'cliente')");
    $stmt->execute([$nome, $email, $senha, $telefone]);

    // Previne session fixation também no cadastro
    session_regenerate_id(true);

    $_SESSION['usuario_id'] = $pdo->lastInsertId();
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['nivel_acesso'] = 'cliente';
    
    redirect('../minha-conta.php');
}
?>