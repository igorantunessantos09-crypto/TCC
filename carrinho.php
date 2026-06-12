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

$tema = $_SESSION['tema'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $tema; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a href="index.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="cart-container" style="margin-top: 4rem;">
        <h1 style="margin-bottom: 2rem; color: var(--gray-900);">Carrinho de Compras</h1>
        
        <?php if (empty($itens)): ?>
            <div style="text-align: center; padding: 4rem; background: var(--white); border-radius: var(--radius-lg);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
                <h2>Seu carrinho está vazio</h2>
                <p style="color: var(--gray-500); margin: 1rem 0 2rem;">Adicione produtos para começar</p>
                <a href="index.php" class="btn-plano">Ver Planos</a>
            </div>
        <?php else: ?>
            <?php foreach ($itens as $item): ?>
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius);">
                </div>
                <div class="cart-item-info">
                    <h3><?php echo $item['nome']; ?></h3>
                    <p style="color: var(--gray-500);"><?php echo $item['descricao']; ?></p>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                        <input type="number" class="cart-qty" data-item-id="<?php echo $item['id']; ?>" 
                               value="<?php echo $item['quantidade']; ?>" min="1" max="10"
                               style="width: 60px; padding: 0.5rem; border: 2px solid var(--gray-300); border-radius: var(--radius-sm);">
                        <button class="btn-remover-item" data-item-id="<?php echo $item['id']; ?>" 
                                style="background: none; border: none; color: var(--danger); cursor: pointer; font-weight: 600;">
                            Remover
                        </button>
                    </div>
                </div>
                <div class="cart-item-price">
                    R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="cart-total">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3>Total</h3>
                        <div class="total-price">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                    </div>
                    <button class="btn-plano" onclick="window.location.href='checkout.php'" style="width: auto; padding: 1rem 3rem;">
                        Finalizar Compra
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="js/carrinho.js"></script>
</body>
</html>