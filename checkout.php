<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$itens = getCarrinhoItens();
$total = array_sum(array_map(function($item) {
    return $item['preco'] * $item['quantidade'];
}, $itens));

if (empty($itens)) {
    redirect('carrinho.php');
}

// Buscar endereço do usuário
$stmt = $pdo->prepare("SELECT * FROM endereco WHERE usuario_id = ? AND principal = 1");
$stmt->execute([$_SESSION['usuario_id']]);
$endereco = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a href="carrinho.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="checkout-container" style="margin-top: 4rem;">
        <h1 style="margin-bottom: 2rem; color: var(--gray-900);">Finalizar Compra</h1>
        
        <form action="php/processar_pedido.php" method="POST">
            <!-- Endereço de Entrega -->
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Endereço de Entrega</h3>
                <?php if ($endereco): ?>
                    <div style="background: var(--gray-100); padding: 1.5rem; border-radius: var(--radius);">
                        <p><strong><?php echo $endereco['rua']; ?>, <?php echo $endereco['numero']; ?></strong></p>
                        <p><?php echo $endereco['complemento']; ?></p>
                        <p><?php echo $endereco['bairro']; ?> - <?php echo $endereco['cidade']; ?>/<?php echo $endereco['estado']; ?></p>
                        <p>CEP: <?php echo $endereco['cep']; ?></p>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" name="cep" required placeholder="00000-000">
                    </div>
                    <div class="form-group">
                        <label>Rua</label>
                        <input type="text" name="rua" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" name="numero" required>
                        </div>
                        <div class="form-group">
                            <label>Complemento</label>
                            <input type="text" name="complemento">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" name="bairro" required>
                        </div>
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="cidade" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" required>
                            <option value="">Selecione</option>
                            <option value="SP">São Paulo</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="MG">Minas Gerais</option>
                            <!-- Adicione mais estados -->
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Método de Pagamento -->
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Método de Pagamento</h3>
                <div class="payment-methods">
                    <label class="payment-method">
                        <input type="radio" name="metodo_pagamento" value="cartao_credito" required>
                        <span>💳</span>
                        <span>Cartão de Crédito</span>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="metodo_pagamento" value="cartao_debito">
                        <span>🏦</span>
                        <span>Cartão de Débito</span>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="metodo_pagamento" value="pix">
                        <span>📱</span>
                        <span>PIX</span>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="metodo_pagamento" value="boleto">
                        <span>📄</span>
                        <span>Boleto Bancário</span>
                    </label>
                </div>
            </div>
            
            <!-- Resumo -->
            <div style="background: var(--gray-100); padding: 2rem; border-radius: var(--radius-lg); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Resumo do Pedido</h3>
                <?php foreach ($itens as $item): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span><?php echo $item['nome']; ?> (<?php echo $item['quantidade']; ?>x)</span>
                    <span>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
                <div style="border-top: 2px solid var(--gray-300); margin-top: 1rem; padding-top: 1rem; display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700;">
                    <span>Total</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
            </div>
            
            <button type="submit" class="btn-plano" style="font-size: 1.2rem; padding: 1.2rem;">
                Confirmar Pedido
            </button>
        </form>
    </div>
    
    <script src="js/carrinho.js"></script>
</body>
</html>