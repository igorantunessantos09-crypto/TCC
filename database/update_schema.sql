-- database/update_schema.sql
-- ATENÇÃO: Execute este script no banco de dados 'tcc' existente

USE `tcc`;

-- Verificar e adicionar colunas na tabela usuario
ALTER TABLE `usuario` 
ADD COLUMN IF NOT EXISTS `nome` VARCHAR(150) AFTER `id`,
ADD COLUMN IF NOT EXISTS `telefone` VARCHAR(20) AFTER `email`,
ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) DEFAULT 'default-avatar.png' AFTER `senha`,
ADD COLUMN IF NOT EXISTS `tema` ENUM('claro', 'escuro') DEFAULT 'claro' AFTER `nivel_acesso`;

-- Atualizar usuários existentes com nome
UPDATE `usuario` SET `nome` = 'Administrador' WHERE `id` = 1 AND `nome` IS NULL;
UPDATE `usuario` SET `nome` = 'Cliente Teste' WHERE `id` = 2 AND `nome` IS NULL;

-- Adicionar coluna de plano na tabela pedido
ALTER TABLE `pedido` 
ADD COLUMN IF NOT EXISTS `plano` ENUM('basico', 'premium', 'empresarial') DEFAULT NULL AFTER `status`;

-- Criar tabela de planos
CREATE TABLE IF NOT EXISTS `planos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(50) NOT NULL,
    `tipo` ENUM('basico', 'premium', 'empresarial') NOT NULL,
    `preco` DECIMAL(10,2) NOT NULL,
    `descricao` TEXT,
    `recursos` TEXT,
    `ativo` BOOLEAN DEFAULT TRUE,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserir planos padrão (apenas se a tabela estiver vazia)
INSERT INTO `planos` (`nome`, `tipo`, `preco`, `descricao`, `recursos`) 
SELECT * FROM (
    SELECT 'Plano Básico' as nome, 'basico' as tipo, 39.90 as preco, 
           'Ideal para residências pequenas' as descricao,
           'Monitoramento básico de fluxo,Alertas por e-mail,Relatório mensal,Suporte por e-mail' as recursos
    UNION ALL
    SELECT 'Plano Premium', 'premium', 79.90,
           'Perfeito para residências e pequenos negócios',
           'Monitoramento avançado,Alertas em tempo real,Relatórios detalhados,Suporte prioritário,Análise de consumo,Histórico de dados'
    UNION ALL
    SELECT 'Plano Empresarial', 'empresarial', 149.90,
           'Solução completa para empresas',
           'Todos os recursos Premium,Monitoramento 24/7,API de integração,Relatórios personalizados,Suporte dedicado,Multi-usuários'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `planos` LIMIT 1
);

-- Adicionar produto de exemplo se não existir
INSERT INTO `produto` (`nome`, `descricao`, `preco`, `quantidade`, `imagem`, `criado_em`)
SELECT * FROM (
    SELECT 'Sensor FlowMonitor Pro' as nome,
           'O FlowMonitor Pro é um dispositivo inteligente de última geração que monitora o fluxo de água em tempo real. Com tecnologia IoT, ele se conecta diretamente ao seu smartphone, enviando alertas instantâneos sobre vazamentos, consumo excessivo e anomalias no fluxo de água.\n\nCaracterísticas:\n• Monitoramento 24/7\n• Instalação simples (não requer encanador)\n• Compatível com canos de 1/2" a 2"\n• Bateria com duração de 2 anos\n• Conexão WiFi\n• App gratuito para iOS e Android' as descricao,
           299.90 as preco,
           50 as quantidade,
           'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500' as imagem,
           CURRENT_TIMESTAMP as criado_em
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `produto` WHERE `nome` = 'Sensor FlowMonitor Pro'
);

-- Criar índices para melhor performance
CREATE INDEX IF NOT EXISTS idx_usuario_email ON `usuario`(`email`);
CREATE INDEX IF NOT EXISTS idx_pedido_usuario ON `pedido`(`usuario_id`);
CREATE INDEX IF NOT EXISTS idx_pedido_status ON `pedido`(`status`);
CREATE INDEX IF NOT EXISTS idx_carrinho_usuario ON `carrinho`(`usuario_id`);
CREATE INDEX IF NOT EXISTS idx_planos_tipo ON `planos`(`tipo`);
CREATE INDEX IF NOT EXISTS idx_planos_ativo ON `planos`(`ativo`);

-- Criar view para relatórios administrativos
CREATE OR REPLACE VIEW `vw_vendas_por_plano` AS
SELECT 
    p.plano,
    COUNT(*) as total_pedidos,
    SUM(p.total) as receita_total,
    AVG(p.total) as ticket_medio,
    COUNT(DISTINCT p.usuario_id) as total_clientes
FROM `pedido` p
WHERE p.status IN ('pago', 'enviado')
GROUP BY p.plano;

-- Criar view para dashboard
CREATE OR REPLACE VIEW `vw_metricas_diarias` AS
SELECT 
    DATE(data_pedido) as data,
    COUNT(*) as total_pedidos,
    SUM(total) as receita,
    COUNT(DISTINCT usuario_id) as clientes_unicos
FROM `pedido`
GROUP BY DATE(data_pedido)
ORDER BY data DESC;