-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/07/2026 às 15:58
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
-- Banco de dados: `tropebook`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipo` enum('livro','trope','lista') NOT NULL,
  `item_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `listas`
--

CREATE TABLE `listas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `icon` varchar(10) DEFAULT '?',
  `curator` varchar(100) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `listas`
--

INSERT INTO `listas` (`id`, `titulo`, `descricao`, `categoria`, `icon`, `curator`, `destaque`, `user_id`, `created_at`) VALUES
(1, 'Royal Romance Trope', 'As melhores histórias envolvendo realeza, coroa e amor impossível entre mundos.', 'Romance', '👑', '@tropebook', 1, NULL, '2026-07-30 12:27:33'),
(2, 'Best Enemies to Lovers 2024', 'A seleção definitiva das histórias onde o ódio vira paixão. Irresistíveis!', 'Romance', '🌹', '@livros_da_pri', 1, NULL, '2026-07-30 12:27:33'),
(3, 'Fake Dating Perfeitos', 'Fingindo amar, mas sentindo de verdade. Uma lista para devorar em um fim de semana.', 'Comédia', '⌨️', '@tropebook', 1, NULL, '2026-07-30 12:27:33'),
(4, 'Dark Academia Romance', 'Bibliotecas antigas, segredos e amores proibidos em cenários acadêmicos encantados.', 'Fantasia', '🌙', '@readswithtati', 0, NULL, '2026-07-30 12:27:33'),
(5, 'Choro Garantido', 'Histórias que vão te fazer chorar da primeira à última página. Emoção pura.', 'Drama', '🌸', '@livros_da_pri', 0, NULL, '2026-07-30 12:27:33'),
(6, 'Fantasy com Romantismo', 'O melhor da fantasia épica com histórias de amor que fazem seu coração acelerar.', 'Fantasia', '✨', '@tropebook', 0, NULL, '2026-07-30 12:27:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `trope_id` int(11) DEFAULT NULL,
  `capa_emoji` varchar(10) DEFAULT '?',
  `descricao` text DEFAULT NULL,
  `paginas` int(11) DEFAULT 0,
  `avaliacao` decimal(3,2) DEFAULT 4.50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor`, `trope_id`, `capa_emoji`, `descricao`, `paginas`, `avaliacao`, `created_at`) VALUES
(1, 'Once & Broken Heart', 'Emily Herry', 2, '📖', 'Uma história envolvente de amor e perda, onde o destino une dois corações quebrados.', 320, 4.70, '2026-07-30 12:27:33'),
(2, 'A Razão do Amor', 'Ali Hazelwood', 1, '📖', 'Um romance inteligente e divertido sobre ciência, amor e segundas chances.', 288, 4.80, '2026-07-30 12:27:33'),
(3, 'Lugar Feliz', 'Emily Henry', 6, '📖', 'Uma história emocionante sobre recomeços e a busca pela felicidade.', 352, 4.60, '2026-07-30 12:27:33'),
(4, 'The Kiss Curse', 'Erin Sterling', 4, '📖', 'Magia, romance e um toque de humor nesta história encantadora.', 304, 4.50, '2026-07-30 12:27:33'),
(5, 'Bride', 'Ali Hazelwood', 7, '📖', 'Um romance proibido entre mundos diferentes, cheio de tensão e paixão.', 336, 4.90, '2026-07-30 12:27:33'),
(6, 'People We Meet', 'Casey McQuiston', 3, '📖', 'Uma jornada de autodescoberta e amizade que se transforma em amor.', 368, 4.70, '2026-07-30 12:27:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tropes`
--

CREATE TABLE `tropes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `icon` varchar(10) DEFAULT '✨',
  `descricao` text NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `emocao` varchar(50) DEFAULT NULL,
  `livros_count` int(11) DEFAULT 0,
  `cor` varchar(20) DEFAULT '#E86FAC',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tropes`
--

INSERT INTO `tropes` (`id`, `nome`, `icon`, `descricao`, `categoria`, `emocao`, `livros_count`, `cor`, `created_at`) VALUES
(1, 'slow burn', '🕰️', 'Romance construído aos poucos, focado na tensão e na expectativa entre os protagonistas.', 'Romance', 'Amor', 128, '#E86FAC', '2026-07-30 12:27:33'),
(2, 'enemies to lovers', '⚔️', 'De inimigos jurados a namorados; a linha tênue entre o amor e o ódio.', 'Romance', 'Amor', 214, '#C94E8A', '2026-07-30 12:27:33'),
(3, 'friends to lovers', '🌸', 'Quando a base de uma grande amizade se transforma em uma grande paixão.', 'Romance', 'Amizade', 176, '#D4499A', '2026-07-30 12:27:33'),
(4, 'fake dating', '💍', 'Fingem estar em um relacionamento por conveniência, até que os sentimentos falsos se tornam reais.', 'Comédia', 'Amor', 98, '#E86FAC', '2026-07-30 12:27:33'),
(5, 'royal romance', '👑', 'Romance envolvendo a realeza. Deveres com a coroa e os desejos do coração.', 'Fantasia', 'Amor', 143, '#9B3A7D', '2026-07-30 12:27:33'),
(6, 'second chance', '🔄', 'Reencontro de ex-namorados. Foca na superação de mágoas do passado e na oportunidade de reconciliação.', 'Drama', 'Superação', 87, '#C94E8A', '2026-07-30 12:27:33'),
(7, 'forbidden love', '🌹', 'Amor proibido. Seja por barreiras sociais, familiares, leis ou rivalidades.', 'Romance', 'Amor', 165, '#8B2FC9', '2026-07-30 12:27:33'),
(8, 'chosen one', '⚡', 'O protagonista descobre ser especial e deve cumprir uma missão maior que si mesmo.', 'Fantasia', 'Superação', 203, '#6B5CE7', '2026-07-30 12:27:33'),
(9, 'found family', '🏠', 'Personagens que não têm família formam laços profundos e se tornam família uns dos outros.', 'Drama', 'Amizade', 119, '#E86FAC', '2026-07-30 12:27:33'),
(10, 'revenge arc', '🗡️', 'Protagonista movida pelo desejo de vingança que vai moldando sua jornada e suas escolhas.', 'Drama', 'Vingança', 92, '#C94E8A', '2026-07-30 12:27:33'),
(11, 'grumpy x sunshine', '☀️', 'Um personagem sombrio e mal-humorado e outro cheio de luz e positividade se apaixonam.', 'Comédia', 'Amor', 134, '#FF9F43', '2026-07-30 12:27:33'),
(12, 'small town', '🏡', 'A magia e os segredos de uma cidade pequena como cenário para romance e descobertas.', 'Romance', 'Amor', 78, '#D4499A', '2026-07-30 12:27:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('usuario','bibliotecario','admin') DEFAULT 'usuario',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrador', 'admin@tropebook.com', '(11) 99999-9999', '$2y$10$eb9ARrFcenbjF6ww.9RN3.jDjyP1Jm5r7JNuHppDokCGnrhw3TXSG', 'admin', '2026-07-30 12:27:33'),
(2, 'Bibliotecário', 'bibliotecario@tropebook.com', '(11) 88888-8888', '$2y$10$eb9ARrFcenbjF6ww.9RN3.jDjyP1Jm5r7JNuHppDokCGnrhw3TXSG', 'bibliotecario', '2026-07-30 12:27:33'),
(3, 'Teste Usuario', 'teste@tropebook.com', '(11) 99999-9999', '$2y$10$G7WPwm6uWSGwoyP3Iremo.3F4WGwaWT/V68ZSFtyqRUBp8ibzp2Ua', 'usuario', '2026-07-30 13:36:54'),
(4, 'Aparecida', 'aparecida@gmail.com', '(11) 99999-9999', '$2y$10$eb9ARrFcenbjF6ww.9RN3.jDjyP1Jm5r7JNuHppDokCGnrhw3TXSG', 'usuario', '2026-07-30 13:43:22'),
(5, 'ana', 'ana.r@gmail.com', '(11) 99999-9999', '$2y$10$FdoYNyuLQHl4yqG/pa1Z1O2dF6fbdxxsY95CVrF3/p8T3ASzq8GpO', 'usuario', '2026-07-30 13:55:41');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorito` (`user_id`,`tipo`,`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `listas`
--
ALTER TABLE `listas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trope_id` (`trope_id`);

--
-- Índices de tabela `tropes`
--
ALTER TABLE `tropes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_unique` (`nome`);

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
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `listas`
--
ALTER TABLE `listas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tropes`
--
ALTER TABLE `tropes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `listas`
--
ALTER TABLE `listas`
  ADD CONSTRAINT `listas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `livros`
--
ALTER TABLE `livros`
  ADD CONSTRAINT `livros_ibfk_1` FOREIGN KEY (`trope_id`) REFERENCES `tropes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
