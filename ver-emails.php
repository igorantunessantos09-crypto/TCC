<?php
require_once 'php/config.php';
require_once 'php/enviar_email.php';

// Para ambiente de desenvolvimento, mostrar os emails
// (Remova esta linha em produção!)
$DEBUG_MODE = true;

if (!$DEBUG_MODE) {
    if (!isLoggedIn() || !isAdmin()) {
        die('Acesso negado');
    }
}

$emails = listarEmailsEnviados();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📧 Emails Enviados - FlowMonitor Debug</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f9ff;
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #0ea5e9;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .email-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .email-item {
            background: white;
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #0ea5e9;
        }
        
        .email-item:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateX(4px);
        }
        
        .email-item .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .email-item .codigo {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: #0ea5e9;
            letter-spacing: 4px;
        }
        
        .email-item .data {
            color: #999;
            font-size: 0.85rem;
        }
        
        .email-item .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #0ea5e9;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0284c7;
        }
        
        .btn-outline {
            background: white;
            color: #0ea5e9;
            border: 2px solid #0ea5e9;
        }
        
        .btn-outline:hover {
            background: #0ea5e9;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #999;
        }
        
        .debug-info {
            background: #fef3c7;
            color: #92400e;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
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
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            border-radius: 16px 16px 0 0;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-close {
            background: #ef4444;
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
        }
        
        iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Central de Emails</h1>
            <p>Sistema de Debug - FlowMonitor</p>
            <span class="badge">🟢 Ambiente de Desenvolvimento</span>
        </div>
        
        <div class="debug-info">
            💡 <strong>Modo Debug:</strong> Os emails estão sendo salvos em arquivo em vez de enviados. 
            Pasta: <code><?php echo __DIR__ . '/emails_enviados/'; ?></code>
        </div>
        
        <!-- Estatísticas -->
        <div class="stats">
            <div class="stat-card">
                <div class="number"><?php echo count($emails); ?></div>
                <div class="label">Total de Emails</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php 
                        $hoje = count(array_filter($emails, function($e) {
                            return strpos($e['data'], date('d/m/Y')) !== false;
                        }));
                        echo $hoje;
                    ?>
                </div>
                <div class="label">Enviados Hoje</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php 
                        $ultimo_codigo = '';
                        foreach ($emails as $email) {
                            // Extrair código do nome do arquivo (últimos 6 dígitos antes de .html)
                            if (preg_match('/_(\d{6})_/', $email['arquivo'], $matches)) {
                                $ultimo_codigo = $matches[1];
                                break;
                            }
                        }
                        echo $ultimo_codigo ?: '---';
                    ?>
                </div>
                <div class="label">Último Código</div>
            </div>
        </div>
        
        <?php if (empty($emails)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h2>Nenhum email enviado ainda</h2>
                <p>Os emails de verificação aparecerão aqui quando alguém se cadastrar.</p>
                <a href="cadastro.php" class="btn btn-primary" style="margin-top: 1rem;">
                    Testar Cadastro →
                </a>
            </div>
        <?php else: ?>
            <div class="email-list">
                <?php foreach ($emails as $index => $email): ?>
                    <div class="email-item">
                        <div class="top-row">
                            <span class="data">📅 <?php echo $email['data']; ?></span>
                            <div class="actions">
                                <button onclick="visualizarEmail('<?php echo $email['link']; ?>')" class="btn btn-primary">
                                    👁️ Visualizar
                                </button>
                                <a href="<?php echo $email['link']; ?>" target="_blank" class="btn btn-outline">
                                    🔗 Abrir
                                </a>
                            </div>
                        </div>
                        <div style="color: #666; font-size: 0.9rem;">
                            Arquivo: <?php echo $email['arquivo']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="back-link">← Voltar ao site</a>
    </div>
    
    <!-- Modal para visualizar email -->
    <div class="modal" id="emailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📧 Visualizar Email</h3>
                <button class="modal-close" onclick="fecharModal()">✕</button>
            </div>
            <div class="modal-body">
                <iframe id="emailFrame" src=""></iframe>
            </div>
        </div>
    </div>
    
    <script>
        function visualizarEmail(link) {
            document.getElementById('emailFrame').src = link;
            document.getElementById('emailModal').classList.add('active');
        }
        
        function fecharModal() {
            document.getElementById('emailModal').classList.remove('active');
            document.getElementById('emailFrame').src = '';
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('emailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });
        
        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModal();
            }
        });
    </script>
</body>
</html>