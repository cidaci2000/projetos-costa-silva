-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/07/2026 às 16:12
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
-- Banco de dados: `loja_esportiva`
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
  `image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `name`, `category`, `price`, `old_price`, `description`, `rating`, `badge`, `image`) VALUES
(1, 'Tênis Asics Gel-Nimbus', 'Calçados', 949.90, 1199.90, 'O máximo em amortecimento e conforto para sua corrida.', 5, 'Destaque', 'data:image/webp;base64,...'),
(2, 'Bola Mikasa V200W', 'Equipamentos', 549.90, 699.90, 'Bola oficial de vôlei aprovada pela FIVB.', 5, 'Profissional', 'data:image/jpeg;base64,...'),
(3, 'Camiseta Brasil Amarela', 'Roupas', 349.90, 399.90, 'Camisa oficial da seleção brasileira, tecido tecnológico.', 5, 'Oficial', 'data:image/jpeg;base64,...'),
(4, 'Manguito N1 Sport', 'Acessórios', 89.90, 119.90, 'Manguito de compressão para vôlei e esportes de alta performance.', 4, 'Novo', 'data:image/webp;base64,...'),
(5, 'Joelheira N1 Sport', 'Acessórios', 129.90, 159.90, 'Proteção e estabilidade para seus joelhos durante o jogo.', 5, 'Vendido+', 'data:image/webp;base64,...'),
(6, 'Bola de Futebol', 'Bolas', 159.90, 199.90, 'Bola resistente para futebol de campo ou society.', 4, 'Promo', 'data:image/jpeg;base64,...'),
(7, 'sapato cinza', 'Calçados', 1200.00, 1000.00, 'produto novo', 5, '', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAJQApgMBIgACEQEDEQH/xAAcAAEAAQUBAQAAAAAAAAAAAAAABgIDBAUHAQj/xAA8EAABAwIEAwUGBAQGAwAAAAABAAIDBBEFEiExBkFREyJhcfAygZGhscFCUtHxFCNi4QcVcpOiwhYkU//EABcBAQEBAQAAAAAAAAAAAAAAAAABAgP/xAAcEQEBAQADAQEBAAAAAAAAAAAAARECITESUUH/2gAMAwEAAhEDEQA/AO4oiICIiAiIgIiICIiAiIgIiICIiAiIg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `name`, `email`, `phone`, `password_hash`, `created_at`, `role`) VALUES
(1, 'APARECIDA DA SILVA FERREIRA', 'aparecida.ferreira@gmail.com', '45999003009', '$2y$10$MLxQbt5.suRilcI2cXLP/OBpeIarD4IvnwqzbdrrhoDbnFezc3rdO', '2026-07-27 11:45:31', 'user'),
(2, 'Administrador', 'admin@bekaesporte.com', '(11) 99999-9999', '$2y$10$HOZENjXjiDqVu8SY6mZZzeBQyFlIRWO9Y.x21yPTD4Ao5oV8rSTpm', '2026-07-27 12:05:14', 'admin'),
(3, 'adminstrador', 'admin@gmail.com', '455555555555555', '$2y$10$HOZENjXjiDqVu8SY6mZZzeBQyFlIRWO9Y.x21yPTD4Ao5oV8rSTpm', '2026-07-27 12:48:42', 'admin');

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
