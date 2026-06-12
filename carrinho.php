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
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .carrinho-page {
            max-width: 900px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .carrinho-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .carrinho-header h1 {
            font-size: 2rem;
            color: var(--text-primary);
        }
        
        .cart-item {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--bg-tertiary);
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-info h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.3rem;
        }
        
        .cart-item-info .item-desc {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .cart-qty {
            width: 60px;
            padding: 0.5rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            text-align: center;
            font-size: 0.9rem;
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        .cart-qty:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .btn-remover-item {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem;
            transition: all 0.2s;
        }
        
        .btn-remover-item:hover {
            color: #dc2626;
            text-decoration: underline;
        }
        
        .cart-item-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            white-space: nowrap;
        }
        
        .cart-total {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 2px solid var(--primary);
        }
        
        .cart-total-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .cart-total-header h3 {
            font-size: 1.3rem;
            color: var(--text-primary);
        }
        
        .cart-total .total-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        .btn-finalizar {
            display: block;
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            margin-top: 1rem;
        }
        
        .btn-finalizar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .empty-cart .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }
        
        .empty-cart h2 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .empty-cart p {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        
        .btn-continuar-comprando {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-continuar-comprando:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .cart-item-image {
                width: 150px;
                height: 150px;
                margin: 0 auto;
            }
            
            .cart-item-actions {
                justify-content: center;
            }
            
            .cart-item-price {
                margin-top: 1rem;
            }
            
            .cart-total .total-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="index.php" class="back-btn" style="position: fixed; top: 1.5rem; left: 1.5rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="carrinho-page" style="margin-top: 5rem;">
        <div class="carrinho-header">
            <h1>🛒 Carrinho de Compras</h1>
        </div>
        
        <?php if (empty($itens)): ?>
            <!-- Carrinho Vazio -->
            <div class="empty-cart">
                <div class="icon">🛒</div>
                <h2>Seu carrinho está vazio</h2>
                <p>Adicione produtos para começar a economizar água!</p>
                <a href="index.php" class="btn-continuar-comprando">Ver Planos</a>
            </div>
        <?php else: ?>
            <!-- Itens do Carrinho -->
            <?php foreach ($itens as $item): ?>
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="<?php echo $item['imagem'] ?: 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500'; ?>" 
                         alt="<?php echo htmlspecialchars($item['nome']); ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500'">
                </div>
                <div class="cart-item-info">
                    <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                    <p class="item-desc"><?php echo substr(htmlspecialchars($item['descricao'] ?? ''), 0, 100); ?>...</p>
                    <div class="cart-item-actions">
                        <input type="number" class="cart-qty" data-item-id="<?php echo $item['id']; ?>" 
                               value="<?php echo $item['quantidade']; ?>" min="1" max="10">
                        <button class="btn-remover-item" data-item-id="<?php echo $item['id']; ?>">
                            🗑️ Remover
                        </button>
                    </div>
                </div>
                <div class="cart-item-price">
                    R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Total e Finalizar -->
            <div class="cart-total">
                <div class="cart-total-header">
                    <h3>Total do Pedido</h3>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">
                        <?php 
                            $total_itens = array_sum(array_column($itens, 'quantidade'));
                            echo $total_itens . ' ' . ($total_itens == 1 ? 'item' : 'itens');
                        ?>
                    </span>
                </div>
                <div class="total-price">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                    ou 12x de R$ <?php echo number_format($total / 12, 2, ',', '.'); ?> sem juros
                </div>
                <a href="checkout.php" class="btn-finalizar">
                    Finalizar Compra 🎉
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="js/carrinho.js"></script>
</body>
</html>