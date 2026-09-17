-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/09/2026 às 15:59
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
-- Banco de dados: `luxury_cars`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `cliente_id`, `veiculo_id`, `nota`, `comentario`, `data_avaliacao`) VALUES
(1, 1, 1, 5, 'Carro incrível! Performance excepcional', '2026-08-27 13:44:36'),
(2, 1, 3, 4, 'Excelente carro, mas preço alto', '2026-08-27 13:44:36'),
(3, 2, 2, 5, 'Melhor conversível que já dirigi', '2026-08-27 13:44:36'),
(4, 3, 4, 5, 'SUV dos sonhos, potência brutal', '2026-08-27 13:44:36'),
(5, 4, 5, 4, 'Design lindo, performance surpreendente', '2026-08-27 13:44:36'),
(6, 5, 6, 5, 'Superesportivo perfeito', '2026-08-27 13:44:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`) VALUES
(1, 'esportivo', 'Veículos esportivos de alta performance'),
(2, 'sedan', 'Sedans de luxo e executivos'),
(3, 'suv', 'SUV e crossovers premium'),
(4, 'luxo', 'Veículos de luxo exclusivos'),
(5, 'superesportivo', 'Veículos superesportivos e hipercarros'),
(6, 'conversível', 'Conversíveis e roadsters');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `endereco` varchar(300) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'Brasil',
  `senha_hash` varchar(255) DEFAULT NULL,
  `recebe_ofertas` tinyint(1) DEFAULT 0,
  `cliente_desde` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `cpf_cnpj`, `data_nascimento`, `genero`, `endereco`, `cidade`, `estado`, `cep`, `pais`, `senha_hash`, `recebe_ofertas`, `cliente_desde`, `ultimo_acesso`, `ativo`) VALUES
(1, 'João Silva', 'joao.silva@email.com', '(11) 99999-9999', '12345678901', '1985-03-15', NULL, NULL, 'São Paulo', 'SP', NULL, 'Brasil', 'hash_senha_1', 1, '2026-08-27 13:44:36', NULL, 1),
(2, 'Maria Santos', 'maria.santos@email.com', '(21) 88888-8888', '98765432102', '1990-07-22', NULL, NULL, 'Rio de Janeiro', 'RJ', NULL, 'Brasil', 'hash_senha_2', 1, '2026-08-27 13:44:36', NULL, 1),
(3, 'Pedro Oliveira', 'pedro.oliveira@email.com', '(41) 77777-7777', '45678912303', '1978-11-30', NULL, NULL, 'Curitiba', 'PR', NULL, 'Brasil', 'hash_senha_3', 0, '2026-08-27 13:44:36', NULL, 1),
(4, 'Ana Costa', 'ana.costa@email.com', '(31) 66666-6666', '78912345604', '1995-09-10', NULL, NULL, 'Belo Horizonte', 'MG', NULL, 'Brasil', 'hash_senha_4', 1, '2026-08-27 13:44:36', NULL, 1),
(5, 'Carlos Lima', 'carlos.lima@email.com', '(45) 55555-5555', '32165498705', '1982-05-05', NULL, NULL, 'Cascavel', 'PR', NULL, 'Brasil', 'hash_senha_5', 1, '2026-08-27 13:44:36', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `descricao`) VALUES
(1, 'taxa_entrega', '500.00', 'Taxa padrão de entrega'),
(2, 'desconto_maximo', '20.00', 'Desconto máximo permitido em %'),
(3, 'prazo_entrega_dias', '30', 'Prazo padrão para entrega em dias úteis'),
(4, 'ticket_medio', '1500000.00', 'Ticket médio da concessionária'),
(5, 'comissao_vendas', '2.50', 'Comissão padrão para vendas em %');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos_entrega`
--

CREATE TABLE `enderecos_entrega` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `apelido` varchar(50) DEFAULT NULL,
  `endereco` varchar(300) NOT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `pais` varchar(100) DEFAULT 'Brasil',
  `principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos_entrega`
--

INSERT INTO `enderecos_entrega` (`id`, `cliente_id`, `apelido`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`, `pais`, `principal`) VALUES
(1, 1, 'Casa', 'Rua das Flores', '100', 'Apto 101', 'Jardim Paulista', 'São Paulo', 'SP', '01234567', 'Brasil', 1),
(2, 1, 'Trabalho', 'Av. Paulista', '2000', 'Andar 15', 'Bela Vista', 'São Paulo', 'SP', '01310000', 'Brasil', 0),
(3, 2, 'Casa', 'Rua do Passeio', '50', 'Casa', 'Copacabana', 'Rio de Janeiro', 'RJ', '22020010', 'Brasil', 1),
(4, 3, 'Casa', 'Rua XV de Novembro', '300', 'Bloco B', 'Centro', 'Curitiba', 'PR', '80010010', 'Brasil', 1),
(5, 4, 'Casa', 'Av. Amazonas', '500', 'Apto 202', 'Sion', 'Belo Horizonte', 'MG', '30310010', 'Brasil', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 0,
  `localizacao` varchar(100) DEFAULT NULL,
  `data_entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_saida` timestamp NULL DEFAULT NULL,
  `status` varchar(30) DEFAULT 'disponivel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `veiculo_id`, `quantidade`, `localizacao`, `data_entrada`, `data_saida`, `status`) VALUES
(1, 1, 2, 'Showroom Principal', '2026-08-27 13:44:36', NULL, 'disponivel'),
(2, 2, 1, 'Showroom Principal', '2026-08-27 13:44:36', NULL, 'disponivel'),
(3, 3, 1, 'Showroom VIP', '2026-08-27 13:44:36', NULL, 'disponivel'),
(4, 4, 1, 'Showroom VIP', '2026-08-27 13:44:36', NULL, 'disponivel'),
(5, 5, 1, 'Showroom VIP', '2026-08-27 13:44:36', NULL, 'disponivel'),
(6, 6, 1, 'Showroom Principal', '2026-08-27 13:44:36', NULL, 'disponivel'),
(7, 7, 2, 'Estoque', '2026-08-27 13:44:36', NULL, 'disponivel'),
(8, 8, 1, 'Estoque', '2026-08-27 13:44:36', NULL, 'disponivel'),
(9, 9, 3, 'Estoque', '2026-08-27 13:44:36', NULL, 'disponivel'),
(10, 10, 1, 'Showroom VIP', '2026-08-27 13:44:36', NULL, 'disponivel');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `cliente_id`, `veiculo_id`, `data_adicao`) VALUES
(1, 1, 3, '2026-08-27 13:44:36'),
(2, 1, 5, '2026-08-27 13:44:36'),
(3, 2, 2, '2026-08-27 13:44:36'),
(4, 2, 6, '2026-08-27 13:44:36'),
(5, 3, 4, '2026-08-27 13:44:36'),
(6, 4, 7, '2026-08-27 13:44:36'),
(7, 5, 10, '2026-08-27 13:44:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `cargo` varchar(100) NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `data_contratacao` date NOT NULL,
  `salario` decimal(12,2) DEFAULT NULL,
  `comissao_percentual` decimal(5,2) DEFAULT 0.00,
  `supervisor_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `nome`, `email`, `telefone`, `cpf`, `cargo`, `departamento`, `data_contratacao`, `salario`, `comissao_percentual`, `supervisor_id`, `ativo`) VALUES
(1, 'Ricardo Gomes', 'ricardo.gomes@luxury.com', '(11) 44444-4444', '11122233301', 'Gerente Geral', 'Administração', '2020-01-15', 15000.00, 0.00, NULL, 1),
(2, 'Fernanda Lima', 'fernanda.lima@luxury.com', '(11) 33333-3333', '22233344402', 'Consultor de Vendas', 'Vendas', '2021-03-10', 8000.00, 2.50, NULL, 1),
(3, 'Roberto Nunes', 'roberto.nunes@luxury.com', '(11) 22222-2222', '33344455503', 'Consultor de Vendas', 'Vendas', '2021-06-20', 8000.00, 2.50, NULL, 1),
(4, 'Patrícia Souza', 'patricia.souza@luxury.com', '(11) 11111-1111', '44455566604', 'Financeiro', 'Financeiro', '2019-08-01', 10000.00, 0.00, NULL, 1),
(5, 'André Martins', 'andre.martins@luxury.com', '(11) 00000-0000', '55566677705', 'Atendimento', 'Atendimento', '2022-02-14', 6000.00, 0.00, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_precos`
--

CREATE TABLE `historico_precos` (
  `id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `preco_antigo` decimal(15,2) NOT NULL,
  `preco_novo` decimal(15,2) NOT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `motivo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `preco_unitario` decimal(15,2) NOT NULL,
  `desconto_unitario` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `veiculo_id`, `quantidade`, `preco_unitario`, `desconto_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 359990.00, 5000.00, 354990.00),
(2, 2, 2, 1, 803717.00, 10000.00, 793717.00),
(3, 3, 4, 1, 3997500.00, 0.00, 3997500.00),
(4, 4, 7, 1, 984990.00, 2000.00, 982990.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `pais_origem` varchar(100) DEFAULT NULL,
  `ano_fundacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `marcas`
--

INSERT INTO `marcas` (`id`, `nome`, `pais_origem`, `ano_fundacao`) VALUES
(1, 'Ford', 'Estados Unidos', 1903),
(2, 'Porsche', 'Alemanha', 1931),
(3, 'Ferrari', 'Itália', 1939),
(4, 'Lamborghini', 'Itália', 1963),
(5, 'Mercedes', 'Alemanha', 1926),
(6, 'Audi', 'Alemanha', 1909),
(7, 'BMW', 'Alemanha', 1916),
(8, 'Lexus', 'Japão', 1989),
(9, 'Jaguar', 'Reino Unido', 1922),
(10, 'Maserati', 'Itália', 1914);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `cliente_id`, `titulo`, `mensagem`, `tipo`, `lida`, `data_envio`) VALUES
(1, 1, 'Oferta Especial', 'Seu carro favorito está com 10% de desconto!', 'promocao', 0, '2026-08-27 13:44:36'),
(2, 2, 'Pedido Enviado', 'Seu Porsche 718 Boxster foi enviado!', 'pedido', 0, '2026-08-27 13:44:36'),
(3, 3, 'Aprovação de Crédito', 'Seu financiamento foi aprovado!', 'financeiro', 0, '2026-08-27 13:44:36'),
(4, 4, 'Novidades', 'Novos modelos 2024 chegaram à nossa loja!', 'novidade', 1, '2026-08-27 13:44:36'),
(5, 1, 'Revisão', 'Não se esqueça de agendar a revisão do seu veículo.', 'manutencao', 0, '2026-08-27 13:44:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `numero_pedido` varchar(50) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_entrega_prevista` date DEFAULT NULL,
  `data_entrega_real` date DEFAULT NULL,
  `status` varchar(30) DEFAULT 'pendente',
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `parcelas` int(11) DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `valor_total` decimal(15,2) NOT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `funcionario_id`, `numero_pedido`, `data_pedido`, `data_entrega_prevista`, `data_entrega_real`, `status`, `forma_pagamento`, `parcelas`, `desconto`, `valor_total`, `observacoes`) VALUES
(1, 1, 2, '2024-000001', '2024-01-15 03:00:00', '2024-02-15', NULL, 'entregue', 'Boleto', 1, 5000.00, 354990.00, 'Entrega rápida'),
(2, 2, 3, '2024-000002', '2024-02-20 03:00:00', '2024-03-20', NULL, 'enviado', 'Cartão de Crédito', 6, 10000.00, 793717.00, 'Enviar para o Rio de Janeiro'),
(3, 3, 2, '2024-000003', '2024-03-10 03:00:00', '2024-04-10', NULL, 'confirmado', 'Financiamento', 12, 0.00, 3997500.00, 'Aguardando aprovação'),
(4, 4, 3, '2024-000004', '2024-04-05 03:00:00', '2024-05-05', NULL, 'pendente', 'Pix', 1, 2000.00, 982990.00, 'Precisa de nota fiscal');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `marca_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano` int(11) NOT NULL,
  `preco` decimal(15,2) NOT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `quilometragem` int(11) DEFAULT 0,
  `combustivel` varchar(30) DEFAULT NULL,
  `motor` varchar(50) DEFAULT NULL,
  `potencia` int(11) DEFAULT NULL,
  `transmissao` varchar(30) DEFAULT NULL,
  `tracao` varchar(30) DEFAULT NULL,
  `portas` int(11) DEFAULT NULL,
  `lugares` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_url` text DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`id`, `marca_id`, `categoria_id`, `modelo`, `ano`, `preco`, `cor`, `quilometragem`, `combustivel`, `motor`, `potencia`, `transmissao`, `tracao`, `portas`, `lugares`, `descricao`, `imagem_url`, `destaque`, `ativo`, `criado_em`) VALUES
(1, 1, 1, 'Mustang GT V8 5.0', 2023, 359990.00, 'Vermelho', 0, 'Gasolina', 'V8 5.0L', 450, 'Automática', 'Traseira', 2, 4, 'O icônico Mustang GT com motor V8 5.0L de 450cv', 'https://images.unsplash.com/photo-1584345604476-8ec5e12e42ad?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(2, 2, 1, '718 Boxster GTS 4.0', 2023, 803717.00, 'Prata', 0, 'Gasolina', 'Boxer 4.0L', 400, 'Automática', 'Traseira', 2, 2, 'Esportivo conversível com motor boxer 4.0L aspirado', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(3, 3, 4, 'Portofino M', 2023, 2200540.00, 'Vermelho', 0, 'Gasolina', 'V8 3.9L', 620, 'Automática', 'Traseira', 2, 4, 'Gran Turismo conversível de luxo da Ferrari', 'https://images.unsplash.com/photo-1592198087100-8d6d9e7d6c0b?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(4, 4, 3, 'Urus', 2023, 3997500.00, 'Amarelo', 0, 'Gasolina', 'V8 4.0L', 650, 'Automática', 'Integral', 4, 5, 'SUV superesportivo da Lamborghini', 'https://images.unsplash.com/photo-1623854762665-9f7d4e0c8d6e?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(5, 5, 4, 'AMG GT Royality', 2023, 2206179.00, 'Preto', 0, 'Gasolina', 'V8 4.0L', 577, 'Automática', 'Traseira', 2, 4, 'Gran Turismo de luxo da Mercedes-AMG', 'https://images.unsplash.com/photo-1617531653332-e614c3a8e8c7?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(6, 6, 1, 'R8 V10', 2023, 1289900.00, 'Cinza', 0, 'Gasolina', 'V10 5.2L', 610, 'Automática', 'Integral', 2, 2, 'Superesportivo com motor V10 central', 'https://images.unsplash.com/photo-1606664515524-ed6f5a63eb5f?w=400&q=80', 1, 1, '2026-08-27 13:44:36'),
(7, 7, 2, 'M5 Competition', 2023, 984990.00, 'Azul', 0, 'Gasolina', 'V8 4.4L', 625, 'Automática', 'Integral', 4, 5, 'Sedan esportivo de alta performance', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&q=80', 0, 1, '2026-08-27 13:44:36'),
(8, 8, 3, 'LX 600', 2023, 689990.00, 'Branco', 0, 'Gasolina', 'V6 3.5L', 409, 'Automática', 'Integral', 4, 7, 'SUV de luxo com capacidade off-road', 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=400&q=80', 0, 1, '2026-08-27 13:44:36'),
(9, 9, 2, 'XF P300', 2023, 459990.00, 'Verde', 0, 'Gasolina', '4.0L', 300, 'Automática', 'Traseira', 4, 5, 'Sedan executivo britânico', 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=400&q=80', 0, 1, '2026-08-27 13:44:36'),
(10, 10, 4, 'GranTurismo', 2023, 1599000.00, 'Azul', 0, 'Gasolina', 'V8 4.7L', 460, 'Automática', 'Traseira', 2, 4, 'Gran Turismo italiano de luxo', 'https://images.unsplash.com/photo-1553440569-bd0f6a1e4c3b?w=400&q=80', 0, 1, '2026-08-27 13:44:36');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_estoque_alerta`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_estoque_alerta` (
`veiculo_id` int(11)
,`modelo` varchar(100)
,`marca` varchar(100)
,`quantidade` int(11)
,`localizacao` varchar(100)
,`status` varchar(30)
,`alerta` varchar(13)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_pedidos_completos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_pedidos_completos` (
`id` int(11)
,`numero_pedido` varchar(50)
,`cliente_nome` varchar(200)
,`cliente_email` varchar(200)
,`funcionario_nome` varchar(200)
,`data_pedido` timestamp
,`data_entrega_prevista` date
,`data_entrega_real` date
,`status` varchar(30)
,`forma_pagamento` varchar(50)
,`valor_total` decimal(15,2)
,`desconto` decimal(10,2)
,`observacoes` text
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_veiculos_detalhes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_veiculos_detalhes` (
`id` int(11)
,`marca` varchar(100)
,`categoria` varchar(50)
,`modelo` varchar(100)
,`ano` int(11)
,`preco` decimal(15,2)
,`cor` varchar(50)
,`quilometragem` int(11)
,`combustivel` varchar(30)
,`motor` varchar(50)
,`potencia` int(11)
,`transmissao` varchar(30)
,`tracao` varchar(30)
,`descricao` text
,`imagem_url` text
,`destaque` tinyint(1)
,`ativo` tinyint(1)
,`estoque_disponivel` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_estoque_alerta`
--
DROP TABLE IF EXISTS `vw_estoque_alerta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_estoque_alerta`  AS SELECT `v`.`id` AS `veiculo_id`, `v`.`modelo` AS `modelo`, `m`.`nome` AS `marca`, `e`.`quantidade` AS `quantidade`, `e`.`localizacao` AS `localizacao`, `e`.`status` AS `status`, CASE WHEN `e`.`quantidade` = 0 THEN 'SEM ESTOQUE' WHEN `e`.`quantidade` <= 2 THEN 'ESTOQUE BAIXO' ELSE 'ESTOQUE OK' END AS `alerta` FROM ((`estoque` `e` join `veiculos` `v` on(`e`.`veiculo_id` = `v`.`id`)) join `marcas` `m` on(`v`.`marca_id` = `m`.`id`)) WHERE `e`.`status` = 'disponivel' ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_pedidos_completos`
--
DROP TABLE IF EXISTS `vw_pedidos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pedidos_completos`  AS SELECT `p`.`id` AS `id`, `p`.`numero_pedido` AS `numero_pedido`, `c`.`nome` AS `cliente_nome`, `c`.`email` AS `cliente_email`, `f`.`nome` AS `funcionario_nome`, `p`.`data_pedido` AS `data_pedido`, `p`.`data_entrega_prevista` AS `data_entrega_prevista`, `p`.`data_entrega_real` AS `data_entrega_real`, `p`.`status` AS `status`, `p`.`forma_pagamento` AS `forma_pagamento`, `p`.`valor_total` AS `valor_total`, `p`.`desconto` AS `desconto`, `p`.`observacoes` AS `observacoes` FROM ((`pedidos` `p` join `clientes` `c` on(`p`.`cliente_id` = `c`.`id`)) join `funcionarios` `f` on(`p`.`funcionario_id` = `f`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_veiculos_detalhes`
--
DROP TABLE IF EXISTS `vw_veiculos_detalhes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_veiculos_detalhes`  AS SELECT `v`.`id` AS `id`, `m`.`nome` AS `marca`, `c`.`nome` AS `categoria`, `v`.`modelo` AS `modelo`, `v`.`ano` AS `ano`, `v`.`preco` AS `preco`, `v`.`cor` AS `cor`, `v`.`quilometragem` AS `quilometragem`, `v`.`combustivel` AS `combustivel`, `v`.`motor` AS `motor`, `v`.`potencia` AS `potencia`, `v`.`transmissao` AS `transmissao`, `v`.`tracao` AS `tracao`, `v`.`descricao` AS `descricao`, `v`.`imagem_url` AS `imagem_url`, `v`.`destaque` AS `destaque`, `v`.`ativo` AS `ativo`, `e`.`quantidade` AS `estoque_disponivel` FROM (((`veiculos` `v` join `marcas` `m` on(`v`.`marca_id` = `m`.`id`)) join `categorias` `c` on(`v`.`categoria_id` = `c`.`id`)) left join `estoque` `e` on(`v`.`id` = `e`.`veiculo_id` and `e`.`status` = 'disponivel')) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `veiculo_id` (`veiculo_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`),
  ADD KEY `idx_clientes_email` (`email`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `enderecos_entrega`
--
ALTER TABLE `enderecos_entrega`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estoque_veiculo` (`veiculo_id`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_favorito` (`cliente_id`,`veiculo_id`),
  ADD KEY `veiculo_id` (`veiculo_id`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Índices de tabela `historico_precos`
--
ALTER TABLE `historico_precos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `veiculo_id` (`veiculo_id`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `veiculo_id` (`veiculo_id`);

--
-- Índices de tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_pedido` (`numero_pedido`),
  ADD KEY `funcionario_id` (`funcionario_id`),
  ADD KEY `idx_pedidos_cliente` (`cliente_id`),
  ADD KEY `idx_pedidos_status` (`status`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_veiculos_marca` (`marca_id`),
  ADD KEY `idx_veiculos_categoria` (`categoria_id`),
  ADD KEY `idx_veiculos_preco` (`preco`),
  ADD KEY `idx_veiculos_destaque` (`destaque`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `enderecos_entrega`
--
ALTER TABLE `enderecos_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `historico_precos`
--
ALTER TABLE `historico_precos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `enderecos_entrega`
--
ALTER TABLE `enderecos_entrega`
  ADD CONSTRAINT `enderecos_entrega_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `funcionarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `historico_precos`
--
ALTER TABLE `historico_precos`
  ADD CONSTRAINT `historico_precos_ibfk_1` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `veiculos`
--
ALTER TABLE `veiculos`
  ADD CONSTRAINT `veiculos_ibfk_1` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `veiculos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
