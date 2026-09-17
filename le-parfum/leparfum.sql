-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/09/2026 às 16:27
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
-- Banco de dados: `leparfum`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`) VALUES
(1, 'Feminino', 'feminino'),
(2, 'Masculino', 'masculino'),
(3, 'Unissex', 'unissex'),
(4, 'Exclusivos', 'exclusivo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome_cliente` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `pagamento` varchar(30) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('processando','transito','entregue','cancelado') DEFAULT 'processando',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `nome_cliente`, `email`, `telefone`, `cep`, `endereco`, `pagamento`, `total`, `status`, `criado_em`) VALUES
(1, 2, 'APARECIDA DA SILVA FERREIRA', 'pati@gmail.com', '(77) 77777-7777', '85807-670', 'araucaria, 123', 'pix', 1019.80, 'processando', '2026-09-15 14:21:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `nome_produto` varchar(120) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `nome_produto`, `preco`, `quantidade`) VALUES
(1, 1, 1, 'Nebula Noir', 459.90, 1),
(2, 1, 4, 'Velvet Aphrodite', 559.90, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_antigo` decimal(10,2) DEFAULT NULL,
  `imagem` varchar(500) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `lancamento` tinyint(1) DEFAULT 0,
  `oferta` tinyint(1) DEFAULT 0,
  `estoque` int(11) DEFAULT 100,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `notas`, `descricao`, `preco`, `preco_antigo`, `imagem`, `categoria_id`, `destaque`, `lancamento`, `oferta`, `estoque`, `criado_em`) VALUES
(1, 'Nebula Noir', 'Pimenta Preta · Couro · Fumaça', 'Uma fragrância intensa e misteriosa.', 459.90, NULL, 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 2, 1, 0, 0, 100, '2026-09-15 12:16:19'),
(2, 'Solar Bloom', 'Jasmim · Néctar de Laranjeira · Ambra', 'Floral luminoso e marcante.', 389.90, NULL, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 1, 1, 1, 0, 100, '2026-09-15 12:16:19'),
(3, 'Mistwood', 'Vetiver · Cedro · Pinho', 'Amadeirado fresco e elegante.', 499.90, 549.90, 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 2, 0, 0, 1, 100, '2026-09-15 12:16:19'),
(4, 'Velvet Aphrodite', 'Rosa Turca · Íris · Baunilha', 'Feminino sofisticado e sensual.', 559.90, NULL, 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 1, 1, 1, 0, 100, '2026-09-15 12:16:19'),
(5, 'Ocean Ghost', 'Algas Marinhas · Sal · Madeira', 'Aquático unissex refrescante.', 429.90, NULL, 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop', 3, 0, 0, 0, 100, '2026-09-15 12:16:19'),
(6, 'Royal Oud', 'Agarwood · Açafrão · Âmbar', 'Oud premium para ocasiões especiais.', 699.90, 799.90, 'https://images.unsplash.com/photo-1590736969956-0f0ea9f2d904?w=400&h=400&fit=crop', 4, 1, 0, 1, 100, '2026-09-15 12:16:19'),
(7, 'Petal Eclipse', 'Peônia · Lychee · Almíscar', 'Floral frutado delicado.', 349.90, NULL, 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 1, 0, 1, 0, 100, '2026-09-15 12:16:19'),
(8, 'Cyber Saffron', 'Açafrão · Pimenta Rosa · Couro', 'Especiado moderno e ousado.', 579.90, NULL, 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 3, 0, 1, 0, 100, '2026-09-15 12:16:19'),
(9, 'Golden Tabac', 'Tabaco · Mel · Especiarias', 'Doce e amadeirado.', 479.90, 529.90, 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 2, 0, 0, 1, 100, '2026-09-15 12:16:19'),
(10, 'Mystic Lily', 'Lírio · Baunilha · Sândalo', 'Floral cremoso e envolvente.', 419.90, NULL, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 1, 0, 0, 0, 100, '2026-09-15 12:16:19'),
(11, 'Crimson Wood', 'Cedro · Cardamomo · Âmbar', 'Amadeirado especiado intenso.', 389.90, NULL, 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop', 2, 0, 0, 0, 100, '2026-09-15 12:16:19'),
(12, 'Soleil Blanc', 'Coco · Flor de Tiaré · Baunilha', 'Solar tropical.', 459.90, NULL, 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 1, 1, 0, 0, 100, '2026-09-15 12:16:19'),
(13, 'Noir Intense', 'Cardamomo · Couro · Cacau', 'Gourmand amadeirado.', 629.90, NULL, 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 2, 1, 1, 0, 100, '2026-09-15 12:16:19'),
(14, 'Rose Noir', 'Rosa Negra · Pimenta · Patchouli', 'Floral escuro e misterioso.', 499.90, 559.90, 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 1, 0, 0, 1, 100, '2026-09-15 12:16:19'),
(15, 'Aqua Vitae', 'Bergamota · Chá Verde · Musgo', 'Cítrico aromático unissex.', 359.90, NULL, 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop', 3, 0, 1, 0, 100, '2026-09-15 12:16:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `cep`, `endereco`, `admin`, `criado_em`) VALUES
(1, 'Administrador', 'admin@leparfum.com', '$2y$10$RWaHZPnSjlIdeKZMePzg5.EMm8BabbWf0MQpEfY22pd10BHsXXQnO', NULL, NULL, NULL, 1, '2026-09-15 12:16:19'),
(2, 'APARECIDA DA SILVA FERREIRA', 'pati@gmail.com', '$2y$10$RWaHZPnSjlIdeKZMePzg5.EMm8BabbWf0MQpEfY22pd10BHsXXQnO', NULL, NULL, NULL, 0, '2026-09-15 14:20:31');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fav` (`usuario_id`,`produto_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

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
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
