-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/07/2026 às 21:05
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
-- Banco de dados: `foods_delivery`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 1, 29.90),
(2, 2, 3, 1, 39.90),
(3, 3, 4, 1, 44.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `motoboys`
--

CREATE TABLE `motoboys` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cnh` varchar(20) NOT NULL,
  `placa` varchar(8) NOT NULL,
  `modelo_moto` varchar(50) NOT NULL,
  `cor_moto` varchar(30) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `avaliacao` decimal(2,1) DEFAULT 0.0,
  `entregas_realizadas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `motoboys`
--

INSERT INTO `motoboys` (`id`, `usuario_id`, `cnh`, `placa`, `modelo_moto`, `cor_moto`, `disponivel`, `avaliacao`, `entregas_realizadas`) VALUES
(1, 5, 'SP12345678901', 'ABC-1234', 'Honda CG 160', 'Vermelha', 1, 4.9, 0),
(2, 6, 'SP98765432109', 'XYZ-5678', 'Yamaha Fazer 150', 'Azul', 1, 4.7, 0),
(3, 8, '44444444444', 'ddd-3333', 'honda', 'azul', 1, 0.0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `restaurante_id` int(11) NOT NULL,
  `motoboy_id` int(11) DEFAULT NULL,
  `status` enum('pendente','confirmado','preparando','saiu_entrega','entregue','cancelado') DEFAULT 'pendente',
  `forma_pagamento` enum('credito','debito','pix','dinheiro') NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `taxa_entrega` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observacao` text DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_entrega` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `restaurante_id`, `motoboy_id`, `status`, `forma_pagamento`, `subtotal`, `taxa_entrega`, `total`, `observacao`, `data_pedido`, `data_entrega`) VALUES
(1, 2, 1, NULL, 'entregue', 'pix', 29.90, 5.00, 34.90, NULL, '2026-07-29 14:47:54', NULL),
(2, 2, 2, NULL, 'entregue', 'credito', 39.90, 8.00, 47.90, NULL, '2026-07-29 14:47:54', NULL),
(3, 2, 2, NULL, 'pendente', 'pix', 44.90, 8.00, 52.90, NULL, '2026-07-29 19:00:18', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `restaurante_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `imagem_url` varchar(500) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `destaque` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `restaurante_id`, `nome`, `descricao`, `preco`, `categoria`, `imagem_url`, `disponivel`, `destaque`) VALUES
(1, 1, 'X-Bacon', 'Hambúrguer com bacon, queijo e molho especial', 29.90, 'Hambúrgueres', 'https://img.freepik.com/free-photo/view-tasty-ham-burger_23-2148882057.jpg', 1, 1),
(2, 1, 'X-Salada', 'Hambúrguer com alface, tomate e maionese', 24.90, 'Hambúrgueres', NULL, 1, 0),
(3, 2, 'Margherita', 'Mussarela, tomate e manjericão', 39.90, 'Pizzas', 'https://img.freepik.com/free-photo/pepperoni-pizza-with-sausages-cheese-dark-background_220768-43.jpg', 1, 1),
(4, 2, 'Pepperoni', 'Mussarela e pepperoni', 44.90, 'Pizzas', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `restaurantes`
--

CREATE TABLE `restaurantes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `descricao` text DEFAULT NULL,
  `endereco` varchar(255) NOT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `imagem_url` varchar(500) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `avaliacao` decimal(2,1) DEFAULT 0.0,
  `delivery_gratis` tinyint(1) DEFAULT 0,
  `tempo_entrega_estimado` int(11) DEFAULT 45,
  `taxa_entrega` decimal(5,2) DEFAULT 0.00,
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `restaurantes`
--

INSERT INTO `restaurantes` (`id`, `usuario_id`, `nome`, `cnpj`, `descricao`, `endereco`, `bairro`, `cidade`, `estado`, `cep`, `telefone`, `imagem_url`, `categoria`, `avaliacao`, `delivery_gratis`, `tempo_entrega_estimado`, `taxa_entrega`, `ativo`, `data_cadastro`) VALUES
(1, 3, 'Burguer House', '12.345.678/0001-90', 'Os melhores hambúrgueres artesanais', 'Av. Paulista, 1000', 'Bela Vista', 'São Paulo', 'SP', NULL, '(11) 99999-8888', NULL, 'Hambúrguer', 4.8, 1, 30, 5.00, 1, '2026-07-29 14:47:54'),
(2, 4, 'Bella Pizza', '98.765.432/0001-10', 'Pizzas napolitanas com ingredientes frescos', 'Rua Augusta, 500', 'Consolação', 'São Paulo', 'SP', NULL, '(11) 99999-7777', NULL, 'Pizza', 4.9, 0, 45, 8.00, 1, '2026-07-29 14:47:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('admin','cliente','motoboy','restaurante') DEFAULT 'cliente',
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cpf`, `telefone`, `tipo`, `ativo`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@foods.com', '$2y$10$SdPxP102eNxjL2TaZuh4PuEDiWRhNEHFUcIEQbcRCwF5x5fI4qRpq', '111.111.111-11', NULL, 'admin', 1, '2026-07-29 14:47:54'),
(2, 'João Silva', 'joao@email.com', '$2y$10$xG29bE/mPuaCzuCJSg4oyuNu5tPTxp65BiaRw7E1K5PjZ07U4pRHm', '123.456.789-00', NULL, 'cliente', 1, '2026-07-29 14:47:54'),
(3, 'Maria Santos', 'maria@email.com', 'e10adc3949ba59abbe56e057f20f883e', '987.654.321-00', NULL, 'cliente', 1, '2026-07-29 14:47:54'),
(4, 'Burguer House', 'contato@burguerhouse.com', '$2y$10$CF0JKGo0U2yN2oxDvv3NVuoa2WN.Guvln/s6LX9MNa1.fOjDgXNmy', '12.345.678/000', NULL, 'restaurante', 1, '2026-07-29 14:47:54'),
(5, 'Bella Pizza', 'contato@bellapizza.com', 'e10adc3949ba59abbe56e057f20f883e', '98.765.432/000', NULL, 'restaurante', 1, '2026-07-29 14:47:54'),
(6, 'Carlos Motoboy', 'carlos@motoboy.com', '$2y$10$WROnZj.yjHhoyYdS1.44o.P8bJA4RFmYxh0OQcqxUi/ndOQrMgnba', '456.789.123-00', NULL, 'motoboy', 1, '2026-07-29 14:47:54'),
(7, 'Ana Entregadora', 'ana@motoboy.com', 'e10adc3949ba59abbe56e057f20f883e', '789.123.456-00', NULL, 'motoboy', 1, '2026-07-29 14:47:54'),
(8, 'ARTHUIR', 'arthur@gmail.com', '$2y$10$icBikkOnRVjbTj.1RZFYfOqhV8XnuSoAZ.eO9P4tlVsN/D8adJhv6', '5555555555', NULL, 'motoboy', 1, '2026-07-29 19:04:24');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `motoboys`
--
ALTER TABLE `motoboys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `restaurante_id` (`restaurante_id`),
  ADD KEY `motoboy_id` (`motoboy_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurante_id` (`restaurante_id`);

--
-- Índices de tabela `restaurantes`
--
ALTER TABLE `restaurantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `motoboys`
--
ALTER TABLE `motoboys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `restaurantes`
--
ALTER TABLE `restaurantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Restrições para tabelas `motoboys`
--
ALTER TABLE `motoboys`
  ADD CONSTRAINT `motoboys_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`motoboy_id`) REFERENCES `motoboys` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `restaurantes`
--
ALTER TABLE `restaurantes`
  ADD CONSTRAINT `restaurantes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
