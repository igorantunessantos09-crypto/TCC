<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['tema'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - FlowMonitor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a href="index.php" class="back-btn" style="position: fixed; top: 1rem; left: 1rem; z-index: 100;">
        ← Voltar
    </a>
    
    <div class="account-container" style="margin-top: 4rem;">
        <h1 style="text-align: center; margin-bottom: 3rem; color: var(--gray-900);">Suporte</h1>
        
        <div class="account-grid">
            <a href="https://tiktok.com/@flowmonitor" target="_blank" class="account-card" style="text-decoration: none;">
                <div class="card-icon" style="background: #000;">🎵</div>
                <div class="card-info">
                    <h3>TikTok</h3>
                    <p>Siga-nos no TikTok</p>
                </div>
            </a>
            
            <a href="https://instagram.com/flowmonitor" target="_blank" class="account-card" style="text-decoration: none;">
                <div class="card-icon" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">📸</div>
                <div class="card-info">
                    <h3>Instagram</h3>
                    <p>Siga-nos no Instagram</p>
                </div>
            </a>
            
            <a href="https://youtube.com/@flowmonitor" target="_blank" class="account-card" style="text-decoration: none;">
                <div class="card-icon" style="background: #ff0000;">▶️</div>
                <div class="card-info">
                    <h3>YouTube</h3>
                    <p>Inscreva-se no nosso canal</p>
                </div>
            </a>
            
            <div class="account-card">
                <div class="card-icon">📱</div>
                <div class="card-info">
                    <h3>FlowMonitor App</h3>
                    <p>Baixe nosso aplicativo</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>