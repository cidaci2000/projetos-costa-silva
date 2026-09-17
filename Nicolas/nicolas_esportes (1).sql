-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/08/2026 às 15:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `nicolas_esportes`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`, `icone`, `created_at`, `updated_at`) VALUES
(1, 'Calçados', 'Tênis e calçados esportivos', 'fa-running', '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(2, 'Bolas', 'Bolas para diversas modalidades', 'fa-basketball-ball', '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(3, 'Equipamentos', 'Equipamentos e acessórios', 'fa-dumbbell', '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(4, 'Natação', 'Produtos para natação', 'fa-swimmer', '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(5, 'Ciclismo', 'Acessórios para ciclismo', 'fa-bicycle', '2026-08-10 10:23:59', '2026-08-10 10:23:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `cpf`, `endereco`, `cidade`, `estado`, `cep`, `senha_hash`, `created_at`, `updated_at`) VALUES
(1, 'JOAO VITOR', 'admin@gmail.com', '(77) 77777-7777', '11111111111111', 'r Araucaria', 'Cascavel', 'pr', '55555-555', '$2y$10$ibNgJtg8BkKlXYg4b.D6ne0tglVJBmDyx0U6sc5EgidetTVgjaEAG', '2026-08-26 11:38:04', '2026-08-26 11:38:04'),
(2, 'nicolas', 'nicolas@gmail.com', '44444', '44444444444444', 'r Araucaria', 'Cascavel', 'pr', '55555-555', '$2y$10$6je1wyfVWEPfAzlrkxzXHuw2AwD3BaLABiQ2CecYZn7CLBnRSkaLG', '2026-08-26 13:17:26', '2026-08-26 13:17:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `numero_pedido` varchar(20) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pendente','pago','enviado','entregue','cancelado') DEFAULT 'pendente',
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `imagem` varchar(255) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `categoria_id`, `nome`, `descricao`, `preco`, `preco_promocional`, `estoque`, `imagem`, `icone`, `badge`, `destaque`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tênis Speed Pro', 'Tênis leve e resistente para corrida', 349.90, 279.90, 50, NULL, NULL, 'novo', 1, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(2, 2, 'Bola Elite 7', 'Bola oficial de basquete', 189.90, NULL, 30, NULL, NULL, 'destaque', 1, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(3, 3, 'Kit Halteres 2x10kg', 'Par de halteres com revestimento', 420.00, 349.00, 20, NULL, NULL, 'oferta', 1, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(4, 4, 'Óculos de Natação', 'Óculos profissional antiembaçante', 99.90, NULL, 40, NULL, NULL, 'importado', 0, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(5, 2, 'Bola de Futebol Pro', 'Bola oficial tamanho 5', 159.90, NULL, 35, NULL, NULL, 'lançamento', 1, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(6, 5, 'Capacito Ciclismo', 'Capacito leve com ajuste', 219.90, NULL, 25, NULL, NULL, 'ultra leve', 0, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(7, 2, 'Luva + Bola Beisebol', 'Kit completo para iniciação', 289.90, NULL, 15, NULL, NULL, 'kit', 0, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59'),
(8, 3, 'Cronômetro Esportivo', 'Digital com timer e alarme', 69.90, NULL, 60, NULL, NULL, 'digital', 0, 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nivel` enum('admin','gerente','funcionario') DEFAULT 'funcionario',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id`, `nome`, `email`, `senha_hash`, `nivel`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@nicolasesportes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-08-10 10:23:59', '2026-08-10 10:23:59');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_produtos_completo`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_produtos_completo` (
`id` int(11)
,`nome` varchar(200)
,`descricao` text
,`preco` decimal(10,2)
,`preco_promocional` decimal(10,2)
,`estoque` int(11)
,`badge` varchar(50)
,`destaque` tinyint(1)
,`ativo` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`categoria_id` int(11)
,`categoria_nome` varchar(100)
,`categoria_icone` varchar(50)
,`preco_atual` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_resumo_clientes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_resumo_clientes` (
`id` int(11)
,`nome` varchar(200)
,`email` varchar(200)
,`total_pedidos` bigint(21)
,`total_gasto` decimal(32,2)
,`ultimo_pedido` timestamp
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_produtos_completo`
--
DROP TABLE IF EXISTS `vw_produtos_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_produtos_completo`  AS SELECT `p`.`id` AS `id`, `p`.`nome` AS `nome`, `p`.`descricao` AS `descricao`, `p`.`preco` AS `preco`, `p`.`preco_promocional` AS `preco_promocional`, `p`.`estoque` AS `estoque`, `p`.`badge` AS `badge`, `p`.`destaque` AS `destaque`, `p`.`ativo` AS `ativo`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `c`.`id` AS `categoria_id`, `c`.`nome` AS `categoria_nome`, `c`.`icone` AS `categoria_icone`, coalesce(`p`.`preco_promocional`,`p`.`preco`) AS `preco_atual` FROM (`produtos` `p` left join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_resumo_clientes`
--
DROP TABLE IF EXISTS `vw_resumo_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_resumo_clientes`  AS SELECT `c`.`id` AS `id`, `c`.`nome` AS `nome`, `c`.`email` AS `email`, count(`p`.`id`) AS `total_pedidos`, coalesce(sum(`p`.`total`),0) AS `total_gasto`, max(`p`.`created_at`) AS `ultimo_pedido` FROM (`clientes` `c` left join `pedidos` `p` on(`c`.`id` = `p`.`cliente_id`)) GROUP BY `c`.`id`, `c`.`nome`, `c`.`email` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_cpf` (`cpf`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido` (`pedido_id`),
  ADD KEY `idx_produto` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_pedido` (`numero_pedido`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_numero` (`numero_pedido`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_destaque` (`destaque`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
