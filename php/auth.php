<?php
require_once 'config.php';

// Processar login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
        $_SESSION['tema'] = $usuario['tema'] ?? 'light';
        
        if ($usuario['nivel_acesso'] === 'admin') {
            redirect('../admin/index.php');
        } else {
            redirect('../minha-conta.php');
        }
    } else {
        $_SESSION['error'] = 'Email ou senha incorretos';
        redirect('../login.php');
    }
}

// Processar registro
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = $_POST['telefone'] ?? null;
    
    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Este email já está cadastrado';
        redirect('../cadastro.php');
    }
    
    $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha, telefone, nivel_acesso) VALUES (?, ?, ?, ?, 'cliente')");
    $stmt->execute([$nome, $email, $senha, $telefone]);
    
    $_SESSION['usuario_id'] = $pdo->lastInsertId();
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['nivel_acesso'] = 'cliente';
    
    redirect('../minha-conta.php');
}
?>