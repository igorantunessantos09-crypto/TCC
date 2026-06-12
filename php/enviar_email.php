<?php
// php/enviar_email.php
function enviarEmail($destinatario, $assunto, $mensagem) {
    // Usando a função mail() do PHP (funciona no XAMPP com configuração)
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: FlowMonitor <noreply@flowmonitor.com>\r\n";
    
    return mail($destinatario, $assunto, $mensagem, $headers);
}

function enviarCodigoVerificacao($email, $codigo) {
    $assunto = "FlowMonitor - Código de Verificação";
    $mensagem = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #0ea5e9;'>FlowMonitor 💧</h1>
            </div>
            <h2>Verificação de Email</h2>
            <p>Seu código de verificação é:</p>
            <div style='background: #f0f9ff; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                <span style='font-size: 32px; font-weight: bold; color: #0ea5e9; letter-spacing: 8px;'>{$codigo}</span>
            </div>
            <p>Este código expira em 10 minutos.</p>
            <p>Se você não solicitou este código, ignore este email.</p>
        </div>
    ";
    
    return enviarEmail($email, $assunto, $mensagem);
}

function enviarLinkRecuperacao($email, $codigo) {
    $link = "http://localhost/mon_água/redefinir-senha.php?codigo={$codigo}&email=" . urlencode($email);
    
    $assunto = "FlowMonitor - Recuperação de Senha";
    $mensagem = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #0ea5e9;'>FlowMonitor 💧</h1>
            </div>
            <h2>Recuperação de Senha</h2>
            <p>Você solicitou a recuperação de senha. Clique no botão abaixo para redefinir:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$link}' style='background: #0ea5e9; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: bold;'>
                    Redefinir Senha
                </a>
            </div>
            <p>Ou copie este código: <strong>{$codigo}</strong></p>
            <p>Este link expira em 30 minutos.</p>
        </div>
    ";
    
    return enviarEmail($email, $assunto, $mensagem);
}
?>