<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

csrfVerify();

$usuario_id = $_SESSION['usuario_id'];
$itens = getCarrinhoItens();

if (empty($itens)) {
    redirect('../carrinho.php');
}

// Calcular total
$total = array_sum(array_map(function($item) {
    return $item['preco'] * $item['quantidade'];
}, $itens));

// Criar pedido
$stmt = $pdo->prepare("INSERT INTO pedido (usuario_id, total, status) VALUES (?, ?, 'pendente')");
$stmt->execute([$usuario_id, $total]);
$pedido_id = $pdo->lastInsertId();

// Adicionar itens ao pedido
foreach ($itens as $item) {
    $subtotal = $item['preco'] * $item['quantidade'];
    $stmt = $pdo->prepare("INSERT INTO item (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $item['preco'], $subtotal]);
}

// Salvar endereço se fornecido
if (isset($_POST['cep']) && !empty($_POST['cep'])) {
    $stmt = $pdo->prepare("INSERT INTO endereco (usuario_id, cep, rua, numero, complemento, bairro, cidade, estado, principal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([
        $usuario_id,
        $_POST['cep'],
        $_POST['rua'],
        $_POST['numero'],
        $_POST['complemento'] ?? null,
        $_POST['bairro'],
        $_POST['cidade'],
        $_POST['estado']
    ]);
}

// Limpar carrinho
$stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);

// Log da ação
$stmt = $pdo->prepare("INSERT INTO logs (usuario_id, acao) VALUES (?, ?)");
$stmt->execute([$usuario_id, "Pedido #$pedido_id criado - Total: R$ $total"]);

// Redirecionar para confirmação
redirect("../confirmacao.php?pedido_id=$pedido_id");
?>