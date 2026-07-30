-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/07/2026 às 12:46
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
-- Banco de dados: `flow_biblioteca`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT '?',
  `categoria` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `resumo` longtext DEFAULT NULL,
  `link_resumo` varchar(500) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id`, `nome`, `autor`, `icon`, `categoria`, `descricao`, `resumo`, `link_resumo`, `disponivel`, `created_at`) VALUES
(1, 'Dom Casmurro', 'Machado de Assis', '📘', 'Clássico', 'Bentinho e Capitu, um dos maiores romances da literatura brasileira.', 'Dom Casmurro é um romance escrito por Machado de Assis, publicado em 1899. A história é narrada por Bentinho, que se autodenomina \"Dom Casmurro\". O livro é famoso pela dúvida sobre a traição de Capitu, a amada do protagonista. A obra é considerada um dos maiores clássicos da literatura brasileira, abordando temas como ciúme, amor e memória.', 'https://pt.wikipedia.org/wiki/Dom_Casmurro', 1, '2026-07-28 15:10:24'),
(2, 'O Alienista', 'Machado de Assis', '📙', 'Clássico', 'A história do médico Simão Bacamarte e seu hospício.', 'O Alienista é uma novela de Machado de Assis publicada em 1882. A história se passa na cidade de Itaguaí e acompanha o médico Simão Bacamarte, que decide estudar a loucura e funda a Casa Verde, um hospício para tratar os \"doidos\" da cidade. A obra é uma crítica à sociedade e à ciência.', 'https://pt.wikipedia.org/wiki/O_Alienista', 1, '2026-07-28 15:10:24'),
(3, 'A Hora da Estrela', 'Clarice Lispector', '📖', 'Romance', 'A história de Macabéa, uma datilógrafa do Rio de Janeiro.', 'A Hora da Estrela é um romance de Clarice Lispector publicado em 1977. A história acompanha Macabéa, uma jovem nordestina que vive no Rio de Janeiro e trabalha como datilógrafa. O livro aborda temas como pobreza, solidão e a busca por identidade. É uma das obras mais importantes da autora.', 'https://pt.wikipedia.org/wiki/A_Hora_da_Estrela', 1, '2026-07-28 15:10:24'),
(4, 'Vidas Secas', 'Graciliano Ramos', '📕', 'Clássico', 'A vida sofrida de uma família no sertão nordestino.', 'Vidas Secas é um romance de Graciliano Ramos publicado em 1938. A história acompanha a família de Fabiano, composta por ele, sua esposa Sinhá Vitória e seus dois filhos, que vivem no sertão nordestino enfrentando a seca e a miséria. O livro é um retrato da realidade social do Nordeste brasileiro.', 'https://pt.wikipedia.org/wiki/Vidas_Secas', 1, '2026-07-28 15:10:24'),
(5, 'Morro dos ventos uivantes', 'Emily Brontë', '📘', 'Classico', 'lançado em 1847, foi o único romance da escritora britânica Emily Brontë. Hoje considerado um clássico da literatura inglesa, recebeu fortes críticas no século XIX', 'https://pt.wikipedia.org/wiki/Wuthering_Heights', '', 1, '2026-07-28 15:26:34');

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
(1, 'Administrador', 'admin@flow.com', '(11) 99999-9999', '$2y$10$RX6FZuW0Nk4Jze1HXwZvn.Yd.tBKAsnyC0/uht19ZX8NsDesxtN1W', 'admin', '2026-07-28 15:10:24'),
(2, 'Bibliotecário', 'bibliotecario@flow.com', '(11) 88888-8888', '$2y$10$RX6FZuW0Nk4Jze1HXwZvn.Yd.tBKAsnyC0/uht19ZX8NsDesxtN1W', 'bibliotecario', '2026-07-28 15:10:24'),
(3, 'administrador', 'admin1@flow.com', '4555555555', '$2y$10$RX6FZuW0Nk4Jze1HXwZvn.Yd.tBKAsnyC0/uht19ZX8NsDesxtN1W', 'admin', '2026-07-28 15:17:37'),
(4, 'Aparecida', 'aparecida@gmail.com', '11111111111111111111', '$2y$10$MlyTh3Jf1QmPhCt/.RijvO4CR/s7Ve5uH0J1939LkHUQ1BRfoqRqe', 'usuario', '2026-07-28 15:23:26');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
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
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
