-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/07/2026 às 16:09
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
-- Banco de dados: `fenix_imobiliaria`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `imoveis`
--

CREATE TABLE `imoveis` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `rooms` int(11) DEFAULT 0,
  `baths` int(11) DEFAULT 0,
  `area` varchar(50) NOT NULL,
  `status` enum('venda','aluguel','terreno') DEFAULT 'venda',
  `featured` tinyint(1) DEFAULT 0,
  `img` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imoveis`
--

INSERT INTO `imoveis` (`id`, `title`, `price`, `location`, `rooms`, `baths`, `area`, `status`, `featured`, `img`, `description`, `created_at`) VALUES
(1, 'Cobertura duplex com vista', 'R$ 1.250.000', 'Vila Mariana, SP', 4, 3, '210m²', 'venda', 1, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop', 'Cobertura duplex com vista panorâmica, 4 suítes, sala ampla e terraço gourmet.', '2026-07-28 13:38:58'),
(2, 'Apartamento moderno 3 quartos', 'R$ 680.000', 'Jardim Paulista, SP', 3, 2, '120m²', 'venda', 0, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&h=300&fit=crop', 'Apartamento moderno com 3 quartos, sendo 1 suíte, sala integrada e varanda.', '2026-07-28 13:38:58'),
(3, 'Casa com piscina e lazer', 'R$ 2.200.000', 'Alphaville, SP', 5, 4, '350m²', 'venda', 1, 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=400&h=300&fit=crop', 'Casa com piscina, área de lazer completa, 5 suítes e amplo jardim.', '2026-07-28 13:38:58'),
(4, 'Flat mobiliado 2 quartos', 'R$ 3.800/mês', 'Itaim Bibi, SP', 2, 1, '68m²', 'aluguel', 0, 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop', 'Flat mobiliado com 2 quartos, sala e cozinha americana. Excelente localização.', '2026-07-28 13:38:58'),
(5, 'Loft alto padrão', 'R$ 4.200/mês', 'Pinheiros, SP', 1, 1, '55m²', 'aluguel', 0, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop', 'Loft alto padrão com acabamento premium, 1 suíte e sala integrada.', '2026-07-28 13:38:58'),
(6, 'Terreno comercial 500m²', 'R$ 890.000', 'Brooklin, SP', 0, 0, '500m²', 'terreno', 0, 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop', 'Terreno comercial com 500m², ideal para construção de edifício comercial.', '2026-07-28 13:38:58'),
(7, 'Terreno residencial 800m²', 'R$ 1.200.000', 'Alphaville, SP', 0, 0, '800m²', 'terreno', 0, 'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?w=400&h=300&fit=crop', 'Terreno residencial com 800m² em condomínio fechado.', '2026-07-28 13:38:58'),
(8, 'Cobertura triplex luxo', 'R$ 3.900.000', 'Morumbi, SP', 6, 5, '480m²', 'venda', 1, 'https://images.unsplash.com/photo-1600607687644-c7171b42498f?w=400&h=300&fit=crop', 'Cobertura triplex de luxo com 6 suítes, piscina privativa e vista para a cidade.', '2026-07-28 13:38:58'),
(9, 'Kitnet reformada', 'R$ 1.900/mês', 'República, SP', 1, 1, '32m²', 'aluguel', 0, 'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?w=400&h=300&fit=crop', 'Kitnet totalmente reformada, mobiliada e com excelente localização.', '2026-07-28 13:38:58'),
(10, 'Terreno com lago 1200m²', 'R$ 2.450.000', 'Atibaia, SP', 0, 0, '1200m²', 'terreno', 1, 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop', 'Terreno com lago particular, 1200m², ideal para chácara ou condomínio.', '2026-07-28 13:38:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('usuario','corretor','admin') DEFAULT 'usuario',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `name`, `email`, `phone`, `cpf`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrador', 'admin@fenix.com', '(11) 99999-9999', '000.000.000-00', '$2y$10$n93dHI0uCQYRM/fo4DE4s.TOTHyWIWEpfAu/a2PF08Lb8t1107N/m', 'admin', '2026-07-28 13:38:58'),
(2, 'Corretor', 'corretor@fenix.com', '(11) 88888-8888', '111.111.111-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'corretor', '2026-07-28 13:38:58'),
(3, 'admin', 'admin@gmail.com', '(11) 99999-9999', NULL, '$2y$10$n93dHI0uCQYRM/fo4DE4s.TOTHyWIWEpfAu/a2PF08Lb8t1107N/m', 'admin', '2026-07-28 14:03:07'),
(4, 'angeline', 'angeline@gmail.com', '(11) 99999-9999', NULL, '$2y$10$Aac3OvMhM6Xu9RusrPx82.Cz8h1c1/s4AkdkdaIMc.EtyL6JlEldi', 'usuario', '2026-07-28 14:04:58'),
(5, 'manu', 'manu@gmail.com', '(11) 99999-9999', NULL, '$2y$10$G69M7k4MNZKtrCtUjaQZhOYRmSQ/PQiLpTVvGDWGo96ev2.k2s96G', 'usuario', '2026-07-28 14:06:35');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `imoveis`
--
ALTER TABLE `imoveis`
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
-- AUTO_INCREMENT de tabela `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
