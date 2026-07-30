-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/07/2026 às 15:03
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
-- Banco de dados: `creamelay`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `badge` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `name`, `category`, `price`, `old_price`, `description`, `rating`, `badge`, `image`, `created_at`) VALUES
(1, 'Copo P – 130ml', 'copos', 9.90, NULL, '1 bola de sorvete à sua escolha', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/sorvete-copo-pequeno-JzrKAi4Lu3ZiWzbp8XqHRi.webp', '2026-07-28 12:18:12'),
(2, 'Copo M – 220ml', 'copos', 15.90, NULL, '2 bolas de sorvete', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/sorvete-copo-medio-gMR26oqQM7s8LX25cFPwDT.webp', '2026-07-28 12:18:12'),
(3, 'Copo G – 330ml', 'copos', 20.90, NULL, '3 bolas de sorvete', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/sorvete-copo-grande-BVMCoGnkjraCuVmGRktLXE.webp', '2026-07-28 12:18:12'),
(4, 'Casquinha Tradicional', 'casquinhas', 8.90, NULL, 'Casquinha crocante com 1 bola', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/sorvete-casquinha-tradicional-Xgx5TS4JWQGUtzKHt4Lc5N.webp', '2026-07-28 12:18:12'),
(5, 'Casquinha Especial', 'casquinhas', 12.90, NULL, 'Premium com chocolate e granulado', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/sorvete-casquinha-especial-5fRxyeZhpeuVpV999VdEVZ.webp', '2026-07-28 12:18:12'),
(6, 'Milk Shake Morango', 'milkshakes', 18.90, NULL, 'Cremoso com chantilly', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/milkshake-morango-FYjwBGVZJ8Sr2EcSQ9YdBm.webp', '2026-07-28 12:18:12'),
(7, 'Milk Shake Chocolate', 'milkshakes', 18.90, NULL, 'Intenso com cobertura especial', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/milkshake-chocolate-KNLJUDi9YYgANCbsMZKXia.webp', '2026-07-28 12:18:12'),
(8, 'Milk Shake Baunilha', 'milkshakes', 18.90, NULL, 'Clássico com granulado colorido', 5, NULL, 'https://d2xsxph8kpxj0f.cloudfront.net/310519663706305270/FyAEvUj8CgCYZdhpmV6D7r/milkshake-baunilha-HViUDeDrhs46vxXuEqjwkw.webp', '2026-07-28 12:18:12'),
(9, 'meu copo', 'copos', 10.00, NULL, 'meu cadastro novo', 5, '', 'https://m.media-amazon.com/images/I/71aDHbhFgOL._AC_SY300_SX300_QL70_ML2_.jpg', '2026-07-28 13:02:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `name`, `email`, `cpf`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrador', 'admin@creamelay.com', '000.000.000-00', '(11) 99999-9999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-07-28 12:18:12'),
(2, 'administrador', 'admin1@gmail.com', '44444444444444', '45555', '$2y$10$oxaI8NxG12/4c1oCmoopZuhDqBhV4bv2He.LnfSRZ6RwVcaOv9R5K', 'admin', '2026-07-28 12:23:12'),
(3, 'JOAO VITOR', 'joao@gmail.com', '7777', '455555555', '$2y$10$vlKmWBgiLmQAqdJKkeO74.xOSBBrWsnkXUHIgcnw3VH7ZLgXQNXvq', 'user', '2026-07-28 12:46:03');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
