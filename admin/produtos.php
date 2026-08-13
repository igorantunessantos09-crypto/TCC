<?php
require_once '../php/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Processar adição de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    csrfVerify();

    if ($_POST['action'] === 'add') {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
        $quantidade = filter_var($_POST['quantidade'] ?? null, FILTER_VALIDATE_INT);
        $imagem = trim($_POST['imagem'] ?? '');
        $categoria_id = $_POST['categoria_id'] ?? null;

        if ($nome === '' || $preco === false || $preco < 0 || $quantidade === false || $quantidade < 0) {
            $_SESSION['mensagem'] = 'Dados inválidos: verifique nome, preço e quantidade.';
            redirect('produtos.php');
        }

        $stmt = $pdo->prepare("INSERT INTO produto (nome, descricao, preco, quantidade, imagem, categoria_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $quantidade, $imagem, $categoria_id]);
        
        $_SESSION['mensagem'] = 'Produto adicionado com sucesso!';
        redirect('produtos.php');
    }
    
    if ($_POST['action'] === 'edit') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
        $quantidade = filter_var($_POST['quantidade'] ?? null, FILTER_VALIDATE_INT);
        $imagem = trim($_POST['imagem'] ?? '');
        $categoria_id = $_POST['categoria_id'] ?? null;

        if (!$id || $nome === '' || $preco === false || $preco < 0 || $quantidade === false || $quantidade < 0) {
            $_SESSION['mensagem'] = 'Dados inválidos: verifique nome, preço e quantidade.';
            redirect('produtos.php');
        }

        $stmt = $pdo->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, quantidade=?, imagem=?, categoria_id=? WHERE id=?");
        $stmt->execute([$nome, $descricao, $preco, $quantidade, $imagem, $categoria_id, $id]);
        
        $_SESSION['mensagem'] = 'Produto atualizado com sucesso!';
        redirect('produtos.php');
    }
    
    if ($_POST['action'] === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['mensagem'] = 'Produto inválido.';
            redirect('produtos.php');
        }

        // Remover itens relacionados
        $stmt = $pdo->prepare("DELETE FROM carrinho WHERE produto_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM item WHERE produto_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM produto WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['mensagem'] = 'Produto excluído com sucesso!';
        redirect('produtos.php');
    }
}

// Buscar produtos
$produtos = $pdo->query("
    SELECT p.*, c.nome as categoria_nome 
    FROM produto p 
    LEFT JOIN categoria c ON p.categoria_id = c.id 
    ORDER BY p.criado_em DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM categoria ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar produto para edição
$produto_edicao = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $produto_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Admin FlowMonitor</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-500);
        }
        
        .produto-grid-admin {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .produto-card-admin {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--gray-200);
        }
        
        .produto-card-admin img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .produto-card-admin h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .produto-card-admin .preco {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .produto-card-admin .acoes {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .btn-edit {
            background: var(--primary);
            color: white;
        }
        
        .btn-delete {
            background: var(--danger);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">💧</div>
                <h2>FlowMonitor</h2>
            </div>
            <nav class="admin-nav">
                <a href="index.php">📊 Dashboard</a>
                <a href="pedidos.php">📦 Pedidos</a>
                <a href="clientes.php">👥 Clientes</a>
                <a href="produtos.php" class="active">🛍️ Produtos</a>
                <a href="../index.php">🏠 Ver Site</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Gerenciar Produtos</h1>
                <button class="btn-logout" onclick="abrirModal('add')" style="background: var(--primary);">
                    + Novo Produto
                </button>
            </header>
            
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert success">
                    <?php 
                        echo $_SESSION['mensagem'];
                        unset($_SESSION['mensagem']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Estatísticas -->
            <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3><?php echo count($produtos); ?></h3>
                        <p>Total de Produtos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>R$ <?php 
                            $total_estoque = 0;
                            foreach ($produtos as $p) {
                                $total_estoque += $p['preco'] * $p['quantidade'];
                            }
                            echo number_format($total_estoque, 2, ',', '.');
                        ?></h3>
                        <p>Valor em Estoque</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?php 
                            $total_unidades = array_sum(array_column($produtos, 'quantidade'));
                            echo $total_unidades;
                        ?></h3>
                        <p>Unidades em Estoque</p>
                    </div>
                </div>
            </div>
            
            <!-- Grid de Produtos -->
            <div class="produto-grid-admin">
                <?php foreach ($produtos as $produto): ?>
                <div class="produto-card-admin">
                    <img src="<?php echo $produto['imagem'] ?: 'https://via.placeholder.com/300x200?text=Sem+Imagem'; ?>" 
                         alt="<?php echo $produto['nome']; ?>"
                         onerror="this.src='https://via.placeholder.com/300x200?text=Sem+Imagem'">
                    
                    <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                    
                    <?php if ($produto['categoria_nome']): ?>
                        <span style="background: var(--primary); color: white; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.8rem;">
                            <?php echo htmlspecialchars($produto['categoria_nome']); ?>
                        </span>
                    <?php endif; ?>
                    
                    <div class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                    
                    <div style="color: var(--gray-500); font-size: 0.9rem;">
                        Estoque: <?php echo $produto['quantidade']; ?> unidades
                    </div>
                    
                    <div style="color: var(--gray-500); font-size: 0.8rem; margin-top: 0.25rem;">
                        Criado em: <?php echo date('d/m/Y', strtotime($produto['criado_em'])); ?>
                    </div>
                    
                    <div class="acoes">
                        <button class="btn-sm btn-edit" onclick="editarProduto(<?php echo $produto['id']; ?>)">
                            ✏️ Editar
                        </button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                            <button type="submit" class="btn-sm btn-delete">🗑️ Excluir</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($produtos)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--gray-500);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                        <h2>Nenhum produto cadastrado</h2>
                        <p>Clique em "Novo Produto" para começar</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Modal Adicionar/Editar Produto -->
    <div class="modal" id="produtoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Novo Produto</h2>
                <button class="modal-close" onclick="fecharModal()">✕</button>
            </div>
            
            <form method="POST" id="produtoForm">
                <?php echo csrfField(); ?>
                <input type="hidden" name="action" value="add" id="formAction">
                <input type="hidden" name="id" id="produtoId">
                
                <div class="form-group">
                    <label>Nome do Produto</label>
                    <input type="text" name="nome" id="produtoNome" required>
                </div>
                
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" id="produtoDescricao" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid var(--gray-300); border-radius: 8px; font-family: inherit;"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Preço (R$)</label>
                        <input type="number" name="preco" id="produtoPreco" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Quantidade em Estoque</label>
                        <input type="number" name="quantidade" id="produtoQuantidade" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>URL da Imagem</label>
                    <input type="url" name="imagem" id="produtoImagem" placeholder="https://exemplo.com/imagem.jpg">
                </div>
                
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="categoria_id" id="produtoCategoria">
                        <option value="">Sem categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-plano" style="margin-top: 1rem;">
                    Salvar Produto
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function abrirModal(modo) {
            const modal = document.getElementById('produtoModal');
            const form = document.getElementById('produtoForm');
            const title = document.getElementById('modalTitle');
            
            if (modo === 'add') {
                title.textContent = 'Novo Produto';
                document.getElementById('formAction').value = 'add';
                form.reset();
                document.getElementById('produtoId').value = '';
            }
            
            modal.classList.add('active');
        }
        
        function fecharModal() {
            document.getElementById('produtoModal').classList.remove('active');
        }
        
        function editarProduto(id) {
            // Buscar dados do produto via AJAX
            fetch(`../php/get_produto.php?id=${id}`)
                .then(response => response.json())
                .then(produto => {
                    document.getElementById('modalTitle').textContent = 'Editar Produto';
                    document.getElementById('formAction').value = 'edit';
                    document.getElementById('produtoId').value = produto.id;
                    document.getElementById('produtoNome').value = produto.nome;
                    document.getElementById('produtoDescricao').value = produto.descricao || '';
                    document.getElementById('produtoPreco').value = produto.preco;
                    document.getElementById('produtoQuantidade').value = produto.quantidade;
                    document.getElementById('produtoImagem').value = produto.imagem || '';
                    document.getElementById('produtoCategoria').value = produto.categoria_id || '';
                    
                    document.getElementById('produtoModal').classList.add('active');
                });
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('produtoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });
    </script>
</body>
</html>