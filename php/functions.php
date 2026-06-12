<?php
// php/functions.php
require_once 'config.php';

function getProduto($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPlanos() {
    global $pdo;
    // Buscar APENAS planos ativos, ordenados por preço
    $stmt = $pdo->query("SELECT * FROM planos WHERE ativo = 1 ORDER BY preco ASC");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Se não tiver planos no banco, retornar planos padrão
    if (empty($result)) {
        return [
            [
                'id' => 1,
                'nome' => 'Plano Básico',
                'tipo' => 'basico',
                'preco' => 39.90,
                'descricao' => 'Ideal para residências pequenas',
                'recursos' => 'Monitoramento básico de fluxo,Alertas por e-mail,Relatório mensal,Suporte por e-mail'
            ],
            [
                'id' => 2,
                'nome' => 'Plano Premium',
                'tipo' => 'premium',
                'preco' => 79.90,
                'descricao' => 'Perfeito para residências e pequenos negócios',
                'recursos' => 'Monitoramento avançado,Alertas em tempo real,Relatórios detalhados,Suporte prioritário,Análise de consumo,Histórico de dados'
            ],
            [
                'id' => 3,
                'nome' => 'Plano Empresarial',
                'tipo' => 'empresarial',
                'preco' => 149.90,
                'descricao' => 'Solução completa para empresas',
                'recursos' => 'Todos os recursos Premium,Monitoramento 24/7,API de integração,Relatórios personalizados,Suporte dedicado,Multi-usuários'
            ]
        ];
    }
    
    return $result;
}

function getCarrinhoCount() {
    if (!isset($_SESSION['usuario_id'])) return 0;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantidade) as total FROM carrinho WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getCarrinhoItens() {
    if (!isset($_SESSION['usuario_id'])) return [];
    
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, p.nome, p.preco, p.imagem, p.descricao 
        FROM carrinho c 
        JOIN produto p ON c.produto_id = p.id 
        WHERE c.usuario_id = ?
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPedidosUsuario($usuario_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, 
               (SELECT GROUP_CONCAT(CONCAT(pr.nome, ' (', i.quantidade, 'x)') SEPARATOR ', ')
                FROM item i 
                JOIN produto pr ON i.produto_id = pr.id 
                WHERE i.pedido_id = p.id) as produtos
        FROM pedido p 
        WHERE p.usuario_id = ? 
        ORDER BY p.data_pedido DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>