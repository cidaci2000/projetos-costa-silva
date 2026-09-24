-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/09/2026 às 12:38
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
-- Banco de dados: `reyvie`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `sessao_id` varchar(100) DEFAULT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `carrinho`
--

INSERT INTO `carrinho` (`id`, `usuario_id`, `sessao_id`, `produto_id`, `quantidade`, `criado_em`) VALUES
(3, NULL, '06d7570e2e35e0bc085334c56979a651', 5, 1, '2026-09-23 14:13:11'),
(8, 4, NULL, 4, 1, '2026-09-24 01:39:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`) VALUES
(1, 'Bases', 'bases'),
(2, 'Corretivos', 'corretivos'),
(3, 'Batom', 'batom'),
(4, 'Sombra', 'sombra'),
(5, 'Blush', 'blush'),
(6, 'Delineador', 'delineador');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(30) DEFAULT 'pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `status`, `criado_em`) VALUES
(1, NULL, 94.80, 'pendente', '2026-09-23 14:12:21'),
(2, 4, 105.80, 'pendente', '2026-09-24 01:37:19'),
(3, 4, 90.80, 'pendente', '2026-09-24 01:39:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 7, 1, 59.90),
(2, 1, 6, 1, 34.90),
(3, 2, 8, 1, 45.90),
(4, 2, 7, 1, 59.90),
(5, 3, 5, 1, 44.90),
(6, 3, 8, 1, 45.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT '?',
  `categoria_id` int(11) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `destaque` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `preco_promocional`, `imagem`, `emoji`, `categoria_id`, `estoque`, `destaque`, `criado_em`) VALUES
(1, 'Base Líquida HD', 'Cobertura alta e acabamento natural para todos os tons.', 89.90, 69.90, 'https://images.unsplash.com/photo-1631214540242-3cd8c4b3b1a8?w=600', '💧', 1, 50, 1, '2026-09-23 12:24:19'),
(2, 'Corretivo Cremoso', 'Alta pigmentação para disfarçar olheiras.', 49.90, NULL, 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600', '🖌️', 2, 40, 1, '2026-09-23 12:24:19'),
(3, 'Batom Matte Vermelho', 'Longa duração e cor intensa.', 39.90, 29.90, 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=600', '💄', 3, 80, 1, '2026-09-23 12:24:19'),
(4, 'Paleta de Sombras Nude', '12 tons neutros altamente pigmentados.', 119.90, NULL, 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=600', '🎨', 4, 30, 1, '2026-09-23 12:24:19'),
(5, 'Blush Rosé', 'Acabamento aveludado e natural.', 54.90, 44.90, 'https://images.unsplash.com/photo-1596704017254-9b121068fb31?w=600', '🌸', 5, 60, 1, '2026-09-23 12:24:19'),
(6, 'Delineador Preto Intenso', 'Ponta fina para traços precisos.', 34.90, NULL, 'https://images.unsplash.com/photo-1583241800698-9c2e0a3f3c9c?w=600', '✏️', 6, 100, 1, '2026-09-23 12:24:19'),
(7, 'Base em Pó Compacto', 'Controle de oleosidade e cobertura leve.', 74.90, 59.90, 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600', '🧴', 1, 35, 0, '2026-09-23 12:24:19'),
(8, 'Corretivo Líquido Verde', 'Neutraliza vermelhidão.', 45.90, NULL, 'https://images.unsplash.com/photo-1590156206657-aec5f6b7f9d1?w=600', '💚', 2, 25, 0, '2026-09-23 12:24:19'),
(9, 'Gloss Labial Transparente', 'Brilho intenso e hidratação.', 29.90, 22.90, 'https://images.unsplash.com/photo-1588514912908-8f58917f5f5e?w=600', '✨', 3, 70, 0, '2026-09-23 12:24:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `is_admin`, `criado_em`) VALUES
(1, 'Administrador', 'admin@reyvie.com', '$2y$10$EqcCcjAxdZqwZKuBMPLCJ.LZK1LFAITP3TFA1r43rwD3fRjRPVeLq', 1, '2026-09-23 12:24:19'),
(2, 'clara', 'clara@gmail.com', '$2y$10$3.s6kLZJCmAN9JsoDFLkOumWxICeK9XRkHlOzn5KYCJwNKYrX8zJu', 0, '2026-09-23 14:15:03'),
(3, 'maria', 'maria@gmail.com', '$2y$10$EqcCcjAxdZqwZKuBMPLCJ.LZK1LFAITP3TFA1r43rwD3fRjRPVeLq', 0, '2026-09-24 01:43:51');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
