<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE usuario SET nome = ?, email = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $_SESSION['usuario_id']]);
    
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuario SET senha = ? WHERE id = ?");
        $stmt->execute([$senha_hash, $_SESSION['usuario_id']]);
    }
    
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['mensagem_config'] = 'Configurações salvas com sucesso! ✅';
    
    redirect('configuracoes.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$mensagem = $_SESSION['mensagem_config'] ?? null;
unset($_SESSION['mensagem_config']);
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/config.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </head>
<body>
    <!-- Menu Toggle -->
    <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">💧</div>
            <h2>FlowMonitor</h2>
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="index.php"><span class="icon">🏠</span> Início</a></li>
            <li><a href="produto.php"><span class="icon">📦</span> Produto</a></li>
            <li><a href="recursos.php"><span class="icon">⚡</span> Recursos</a></li>
            <li><a href="suporte.php"><span class="icon">💬</span> Suporte</a></li>
            <li><a href="minha-conta.php"><span class="icon">👤</span> Minha Conta</a></li>
            <li><a href="pedidos.php"><span class="icon">📋</span> Meus Pedidos</a></li>
            <li><a href="configuracoes.php" class="active"><span class="icon">⚙️</span> Configurações</a></li>
            
            <?php if (isAdmin()): ?>
                <li style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--primary);">
                    <a href="admin/index.php" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                        <span class="icon">📊</span> Dashboard Admin
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="settings-page">
            <!-- Cabeçalho -->
            <div class="settings-header">
                <a href="minha-conta.php" class="back-btn" style="margin-bottom: 1.5rem;">
                    ← Voltar
                </a>
                <h1>⚙️ Configurações</h1>
                <p>Personalize sua experiência no FlowMonitor</p>
            </div>
            
            <?php if ($mensagem): ?>
                <div class="alert-success">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Tema -->
            <div class="setting-card">
                <h3>
                    <span class="icon">🎨</span> Aparência
                </h3>
                <div class="toggle-row">
                    <div class="toggle-label">
                        <span>Modo Escuro</span>
                        <span>Ative para reduzir o cansaço visual</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="theme-toggle" <?php echo ($_SESSION['tema'] ?? 'light') === 'dark' ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            
            <!-- Editar Perfil -->
            <div class="setting-card">
                <h3>
                    <span class="icon">👤</span> Informações Pessoais
                </h3>
                <form method="POST" action="configuracoes.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome completo</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" 
                                   required placeholder="Seu nome">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" 
                                   required placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="password" name="senha" placeholder="Deixe em branco para manter a atual" minlength="6">
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Mínimo de 6 caracteres
                        </span>
                    </div>
                    <button type="submit" class="btn-save">
                        💾 Salvar Alterações
                    </button>
                </form>
            </div>
            
            <!-- Informações da Conta -->
            <div class="setting-card">
                <h3>
                    <span class="icon">ℹ️</span> Informações da Conta
                </h3>
                <div style="color: var(--text-secondary); line-height: 2;">
                    <div><strong>Tipo de conta:</strong> <?php echo ucfirst($usuario['nivel_acesso'] ?? 'Cliente'); ?></div>
                    <div><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($usuario['criado_em'] ?? 'now')); ?></div>
                </div>
            </div>

                        <!-- Endereços -->
            <div class="setting-card">
                <h3>
                    <span class="icon">📍</span> Endereços
                </h3>
                
                <?php
                // Buscar endereços do usuário
                $stmt = $pdo->prepare("SELECT * FROM endereco WHERE usuario_id = ? ORDER BY tipo ASC");
                $stmt->execute([$_SESSION['usuario_id']]);
                $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <?php foreach ($enderecos as $endereco): ?>
                    <div style="background: var(--bg-tertiary); padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; border: 2px solid <?php echo $endereco['tipo'] === 'principal' ? 'var(--primary)' : 'var(--border-color)'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <strong style="color: var(--text-primary);">
                                <?php echo $endereco['tipo'] === 'principal' ? '📍 Principal' : '📌 Secundário'; ?>
                            </strong>
                            <div style="display: flex; gap: 0.5rem;">
                                <button onclick="editarEndereco(<?php echo $endereco['id']; ?>)" 
                                        style="background: var(--primary); color: white; border: none; padding: 0.4rem 1rem; border-radius: 20px; cursor: pointer; font-size: 0.85rem;">
                                    Editar
                                </button>
                                <?php if ($endereco['tipo'] !== 'principal'): ?>
                                    <button onclick="tornarPrincipal(<?php echo $endereco['id']; ?>)" 
                                            style="background: var(--success); color: white; border: none; padding: 0.4rem 1rem; border-radius: 20px; cursor: pointer; font-size: 0.85rem;">
                                        Tornar Principal
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p style="color: var(--text-secondary); margin: 0;">
                            <?php echo $endereco['rua'] . ', ' . $endereco['numero']; ?>
                            <?php echo $endereco['complemento'] ? ' - ' . $endereco['complemento'] : ''; ?><br>
                            <?php echo $endereco['bairro'] . ' - ' . $endereco['cidade'] . '/' . $endereco['estado']; ?><br>
                            CEP: <?php echo $endereco['cep']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($enderecos) < 2): ?>
                    <button onclick="mostrarFormEndereco()" 
                            style="width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        + Adicionar Endereço Secundário
                    </button>
                <?php endif; ?>
            </div>

            <!-- Form de Endereço (oculto) -->
            <div id="formEndereco" style="display: none;" class="setting-card">
                <h3>📝 <?php echo count($enderecos) < 1 ? 'Adicionar' : 'Editar'; ?> Endereço</h3>
                <form method="POST" action="php/salvar_endereco.php">
                    <input type="hidden" name="endereco_id" id="enderecoId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" name="cep" id="cep" required placeholder="00000-000">
                        </div>
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" name="numero" required placeholder="Nº">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Rua</label>
                        <input type="text" name="rua" id="rua" required placeholder="Nome da rua">
                    </div>
                    
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="complemento" placeholder="Apto, Bloco, etc.">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" name="bairro" id="bairro" required>
                        </div>
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="cidade" id="cidade" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" id="estado" required>
                            <option value="">Selecione</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-save">💾 Salvar Endereço</button>
                </form>
            </div>

            <script>
                function mostrarFormEndereco() {
                    document.getElementById('formEndereco').style.display = 'block';
                    document.getElementById('formEndereco').scrollIntoView({ behavior: 'smooth' });
                }
                
                function editarEndereco(id) {
                    // Buscar dados do endereço
                    fetch(`php/buscar_endereco.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('enderecoId').value = data.id;
                            document.getElementById('cep').value = data.cep;
                            document.getElementById('rua').value = data.rua;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.cidade;
                            document.getElementById('estado').value = data.estado;
                            document.querySelector('input[name="numero"]').value = data.numero;
                            document.querySelector('input[name="complemento"]').value = data.complemento || '';
                            
                            mostrarFormEndereco();
                        });
                }
                
                function tornarPrincipal(id) {
                    if (confirm('Tornar este endereço como principal?')) {
                        fetch('php/tornar_principal.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `endereco_id=${id}`
                        }).then(() => location.reload());
                    }
                }
                
                // Buscar CEP automaticamente
                document.getElementById('cep')?.addEventListener('blur', function() {
                    const cep = this.value.replace(/\D/g, '');
                    if (cep.length === 8) {
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    document.getElementById('rua').value = data.logradouro || '';
                                    document.getElementById('bairro').value = data.bairro || '';
                                    document.getElementById('cidade').value = data.localidade || '';
                                    document.getElementById('estado').value = data.uf || '';
                                }
                            });
                    }
                });
            </script>
        </div>
    </main>
    
    <script>
        const usuarioLogado = true;
        const usuarioId = <?php echo $_SESSION['usuario_id']; ?>;
    </script>
    <script src="js/script.js"></script>
</body>
</html>