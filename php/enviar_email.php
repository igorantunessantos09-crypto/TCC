<?php
// php/enviar_email.php

/**
 * Função que simula envio de email salvando em arquivo
 * Para ambiente de desenvolvimento/TCC
 */
function enviarEmail($destinatario, $assunto, $mensagem) {
    // Criar pasta de emails se não existir
    $pasta_emails = __DIR__ . '/../emails_enviados/';
    if (!is_dir($pasta_emails)) {
        mkdir($pasta_emails, 0777, true);
    }
    
    // Nome do arquivo com timestamp
    $arquivo = $pasta_emails . date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';
    
    // Conteúdo completo do email
    $conteudo = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .email-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e5e5; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h2>FlowMonitor 💧</h2>
        </div>
        <p><strong>Para:</strong> {$destinatario}</p>
        <p><strong>Assunto:</strong> {$assunto}</p>
        <hr>
        {$mensagem}
        <div class='footer'>
            <p>Este é um email simulado para ambiente de desenvolvimento.</p>
            <p>FlowMonitor © " . date('Y') . "</p>
        </div>
    </div>
</body>
</html>";
    
    // Salvar arquivo
    file_put_contents($arquivo, $conteudo);
    
    // Também salvar um log
    $log = date('Y-m-d H:i:s') . " | Para: {$destinatario} | Assunto: {$assunto}\n";
    file_put_contents($pasta_emails . 'log.txt', $log, FILE_APPEND);
    
    return true;
}

/**
 * Enviar código de verificação por email
 */
function enviarCodigoVerificacao($email, $codigo) {
    $assunto = "FlowMonitor - Código de Verificação";
    $mensagem = "
        <div style='text-align: center; padding: 20px;'>
            <h2 style='color: #0ea5e9;'>Verificação de Email</h2>
            <p style='font-size: 16px;'>Seu código de verificação é:</p>
            <div style='background: #f0f9ff; padding: 30px; margin: 20px 0; border-radius: 15px;'>
                <span style='font-size: 48px; font-weight: bold; color: #0ea5e9; letter-spacing: 10px;'>{$codigo}</span>
            </div>
            <p style='color: #666;'>Este código expira em <strong>10 minutos</strong>.</p>
            <p style='color: #999; font-size: 14px;'>Se você não solicitou este código, ignore este email.</p>
        </div>
    ";
    
    return enviarEmail($email, $assunto, $mensagem);
}

/**
 * Enviar link de recuperação de senha
 */
function enviarLinkRecuperacao($email, $codigo) {
    $link = "http://localhost/mon_água/redefinir-senha.php?codigo={$codigo}&email=" . urlencode($email);
    
    $assunto = "FlowMonitor - Recuperação de Senha";
    $mensagem = "
        <div style='text-align: center; padding: 20px;'>
            <h2 style='color: #0ea5e9;'>Recuperação de Senha</h2>
            <p style='font-size: 16px;'>Você solicitou a recuperação de senha.</p>
            <p style='font-size: 16px;'>Seu código de recuperação é:</p>
            <div style='background: #f0f9ff; padding: 30px; margin: 20px 0; border-radius: 15px;'>
                <span style='font-size: 48px; font-weight: bold; color: #0ea5e9; letter-spacing: 10px;'>{$codigo}</span>
            </div>
            <div style='margin: 30px 0;'>
                <a href='{$link}' style='background: #0ea5e9; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px;'>
                    Redefinir Senha
                </a>
            </div>
            <p style='color: #666;'>Este código expira em <strong>30 minutos</strong>.</p>
            <p style='color: #999; font-size: 14px;'>Link: {$link}</p>
        </div>
    ";
    
    return enviarEmail($email, $assunto, $mensagem);
}

/**
 * Mostrar emails enviados (para debug)
 */
function listarEmailsEnviados() {
    $pasta = __DIR__ . '/../emails_enviados/';
    $arquivos = glob($pasta . '*.html');
    $emails = [];
    
    foreach ($arquivos as $arquivo) {
        $emails[] = [
            'arquivo' => basename($arquivo),
            'data' => date('d/m/Y H:i:s', filemtime($arquivo)),
            'link' => 'emails_enviados/' . basename($arquivo)
        ];
    }
    
    // Ordenar do mais recente
    usort($emails, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });
    
    return $emails;
}
?>