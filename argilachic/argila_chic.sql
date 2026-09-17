-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/09/2026 às 13:56
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
-- Banco de dados: `argila_chic`
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
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `slug`, `nome`) VALUES
(1, 'vasos', 'Vasos'),
(2, 'tigelas', 'Tigelas'),
(3, 'pratos', 'Pratos'),
(4, 'decoracao', 'Decoração');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 2, 1, 65.00),
(2, 1, 4, 1, 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `numero_pedido` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `frete` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('confirmado','preparando','transito','entregue','cancelado') DEFAULT 'confirmado',
  `transportadora` varchar(100) DEFAULT 'Sedex',
  `codigo_rastreio` varchar(50) DEFAULT NULL,
  `previsao_entrega` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `numero_pedido`, `subtotal`, `frete`, `total`, `status`, `transportadora`, `codigo_rastreio`, `previsao_entrega`, `criado_em`) VALUES
(1, 2, 'PED-2026-431242', 185.00, 0.00, 185.00, 'confirmado', 'Sedex', 'BR906520226BR', '2026-09-21', '2026-09-16 11:46:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `descricao_longa` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `imagem` varchar(255) NOT NULL,
  `galeria` text DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `descricao_longa`, `categoria_id`, `preco`, `preco_promocional`, `estoque`, `imagem`, `galeria`, `destaque`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 'Prato Artesanal', 'Prato elegante com design artesanal', 'Prato artesanal feito à mão com argila de alta qualidade, acabamento fosco e detalhes únicos. Diâmetro: 27cm.', 3, 89.90, NULL, 15, 'prato-artesanal.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(2, 'Tigela Terracota', 'Tigela feita manualmente com acabamento fino', 'Tigela em terracota natural, perfeita para saladas, sobremesas ou decoração. Capacidade: 500ml.', 2, 65.00, NULL, 19, 'tigela-terracota.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 11:46:41'),
(3, 'Vaso Decorativo', 'Vaso com padrão único e artístico', 'Vaso decorativo com textura trabalhada à mão, ideal para plantas pequenas ou como peça escultural.', 1, 45.90, NULL, 12, 'vaso-decorativo.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(4, 'Vaso Praia', 'Vaso inspirado na estética praiana', 'Vaso alto inspirado na estética praiana, com tons de areia e acabamento suave. Altura: 35cm.', 1, 120.00, NULL, 7, 'vaso-praia.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 11:46:41'),
(5, 'Tigela Bege', 'Tigela em tom bege natural', 'Tigela em tom bege natural, minimalista e versátil, perfeita para o dia a dia.', 2, 55.00, NULL, 18, 'tigela-bege.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(6, 'Escultura Sofisticada', 'Peça decorativa abstrata sofisticada', 'Escultura decorativa abstrata, peça única assinada pelo artista. Altura: 40cm.', 4, 150.00, NULL, 5, 'escultura.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(7, 'Prato Branco', 'Prato branco com textura elegante', 'Prato branco com textura canelada, elegante e atemporal. Diâmetro: 25cm.', 3, 38.50, NULL, 25, 'prato-branco.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(8, 'Vaso Alto', 'Vaso alto com proporções clássicas', 'Vaso alto com proporções clássicas gregas, acabamento em argila crua. Altura: 50cm.', 1, 145.00, NULL, 7, 'vaso-alto.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(9, 'Tigela Pequena', 'Tigela pequena perfeita para decoração', 'Tigela pequena, perfeita para petiscos ou como porta-joias. Diâmetro: 10cm.', 2, 35.00, NULL, 30, 'tigela-pequena.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(10, 'Luminária Cerâmica', 'Luminária artesanal em cerâmica', 'Luminária artesanal em cerâmica com perfurações que criam efeitos de luz únicos. Altura: 30cm.', 4, 180.00, NULL, 4, 'luminaria.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(11, 'Prato Artístico', 'Prato com arte manual única', 'Prato com pintura manual exclusiva, cada peça é única. Diâmetro: 30cm.', 3, 72.00, NULL, 10, 'prato-artistico.jpg', NULL, 0, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02'),
(12, 'Vaso Ondulado', 'Vaso com design ondulado moderno', 'Vaso com design ondulado moderno, acabamento em esmalte brilhante. Altura: 28cm.', 1, 95.00, NULL, 9, 'vaso-ondulado.jpg', NULL, 1, 1, '2026-09-16 10:56:02', '2026-09-16 10:56:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `tipo` enum('cliente','admin') DEFAULT 'cliente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `data_nascimento`, `endereco`, `cidade`, `estado`, `cep`, `tipo`, `criado_em`) VALUES
(1, 'Administrador', 'admin@argilachic.com', '$2y$10$5Z3OvIcOyn3DbwhihOmFX.Jajtx4/zS1mRA1DbpNu5XT.LEBqoOwq', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2026-09-16 10:56:02'),
(2, 'JOAO VITOR', 'joaovictor@gmail.com', '$2y$10$5Z3OvIcOyn3DbwhihOmFX.Jajtx4/zS1mRA1DbpNu5XT.LEBqoOwq', '(77) 77777-7777', '1997-08-03', 'araucaria, 123', 'Cascavel', 'PR', '85807-670', 'cliente', '2026-09-16 11:46:20');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_pedido` (`numero_pedido`),
  ADD KEY `usuario_id` (`usuario_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
