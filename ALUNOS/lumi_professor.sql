-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/09/2026 às 19:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lumi_professor`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT 'padrao.png',
  `data_nascimento` date DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `turma` varchar(100) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `senha_visivel` varchar(255) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `professor_id`, `nome`, `usuario`, `senha`, `foto`, `data_nascimento`, `turma_id`, `turma`, `data_cadastro`, `senha_visivel`, `ultimo_acesso`) VALUES
(13, 1, 'Felipe', 'Sec', 'sec123', 'padrao.png', '2020-08-01', 5, NULL, '2026-07-30 06:47:18', 'Sec01', NULL),
(14, 2, 'Luana', 'Luana01', '$2y$10$secMaTIOaviwtQm.LLDN7OZ/q.iUB0f63zWDkuVXWIO6tn795FAba', 'padrao.png', '2020-11-02', 7, NULL, '2026-08-24 02:39:13', 'Luana01', '2026-09-14 17:24:40'),
(15, 2, 'Ana', 'Ana02', '$2y$10$n206XBuUsEvJheG0ymi.8O91Ot44lm5vF4LXhXBZaKatUhc1iRPI2', 'padrao.png', '2020-03-01', 7, NULL, '2026-08-25 00:20:32', 'Ana02', '2026-08-26 13:28:04'),
(16, 2, 'Felipe', 'Felipe03', '$2y$10$1YOA0w0ViFRUlRmcDz5EMeP6HaRxxvoS13xExZhtMzxmNdX1jfQIO', 'padrao.png', '2020-02-01', 7, NULL, '2026-08-25 00:23:52', 'Felipe03', NULL),
(21, 3, 'Ana', 'Ana04', '$2y$10$ZlS3mjbRX4MzkRt/rLXGyOyjQhnN6vQh/U6tvs7w..DLoPUDfB7qu', 'padrao.png', NULL, 9, NULL, '2026-08-26 19:18:25', 'Ana04', '2026-09-17 20:17:03'),
(22, 3, 'Joao', 'Joao05', '$2y$10$pH1Vlw0FOEj6LiIrwRKvg.MtrM16fuwml35kc6JT4qHwk.wMN./.i', 'padrao.png', NULL, 9, NULL, '2026-08-26 19:21:33', 'Joao05', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno_turma`
--

CREATE TABLE `aluno_turma` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `desempenho`
--

CREATE TABLE `desempenho` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `licao_id` int(11) NOT NULL,
  `resultado` varchar(50) DEFAULT NULL,
  `pontuacao` int(11) DEFAULT 0,
  `tempo_gasto` int(11) DEFAULT 0,
  `data_realizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `licoes`
--

CREATE TABLE `licoes` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `visualizada` tinyint(1) DEFAULT 0,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT 'padrao.png',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `nome`, `email`, `senha`, `foto`, `data_cadastro`) VALUES
(1, 'felipe', 'felipe3@gmail.com', '$2y$10$kBwG1J6qWj.gJ8pyVPRL1Oc8NUF48I4b0jje9hljBTLbKJr4hRPhK', 'padrao.png', '2026-07-30 00:54:08'),
(2, 'Silvia de Barros', 'silviab@gmail.com', '$2y$10$OAflshcqxHF/3wNRU/YFiOJ2WCgOduyqHZQ1GBEkPrWsKHoEwyRkC', 'padrao.png', '2026-08-24 02:37:21'),
(3, 'Pedro Souza', 'pedro@gmail.com', '$2y$10$f0YJjAHcN0bZct69mK9IaeGlV4MwIY9IRPEh.hkpZqfSxqYJB.nIK', 'padrao.png', '2026-08-26 19:17:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso`
--

CREATE TABLE `progresso` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `estrutura_id` int(11) NOT NULL DEFAULT 1,
  `nivel_atual` int(11) DEFAULT 1,
  `estrelas` int(11) DEFAULT 0,
  `moedas` int(11) DEFAULT 0,
  `licoes_concluidas` int(11) DEFAULT 0,
  `acertos` int(11) DEFAULT 0,
  `erros` int(11) DEFAULT 0,
  `tempo_estudo` int(11) DEFAULT 0,
  `ultimo_acesso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `progresso`
--

INSERT INTO `progresso` (`id`, `aluno_id`, `estrutura_id`, `nivel_atual`, `estrelas`, `moedas`, `licoes_concluidas`, `acertos`, `erros`, `tempo_estudo`, `ultimo_acesso`) VALUES
(6, 13, 1, 1, 0, 0, 6, 0, 0, 0, NULL),
(7, 13, 1, 2, 0, 0, 8, 0, 0, 0, NULL),
(8, 13, 1, 3, 0, 0, 1, 0, 0, 0, NULL),
(22, 14, 1, 1, 0, 0, 13, 0, 0, 0, NULL),
(24, 14, 1, 2, 0, 0, 12, 0, 0, 0, NULL),
(25, 15, 1, 1, 0, 0, 0, 0, 0, 0, NULL),
(26, 14, 1, 3, 0, 0, 6, 0, 0, 0, NULL),
(27, 16, 1, 1, 0, 0, 0, 0, 0, 0, NULL),
(28, 14, 1, 4, 0, 0, 4, 0, 0, 0, NULL),
(31, 15, 1, 2, 0, 0, 1, 0, 0, 0, NULL),
(32, 21, 1, 1, 0, 0, 3, 0, 0, 0, NULL),
(48, 14, 1, 5, 0, 0, 2, 0, 0, 0, NULL),
(64, 21, 1, 2, 0, 0, 13, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `professor_id`, `nome`, `descricao`) VALUES
(5, 1, '1ºA', NULL),
(6, 1, '1ºC', NULL),
(7, 2, '1 ano D', NULL),
(8, 2, '1 ano A', NULL),
(9, 3, '1 ano D', NULL),
(10, 3, '1 ano A', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Índices de tabela `aluno_turma`
--
ALTER TABLE `aluno_turma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `desempenho`
--
ALTER TABLE `desempenho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `licao_id` (`licao_id`);

--
-- Índices de tabela `licoes`
--
ALTER TABLE `licoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `progresso`
--
ALTER TABLE `progresso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_nivel_unico` (`aluno_id`,`estrutura_id`,`nivel_atual`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `aluno_turma`
--
ALTER TABLE `aluno_turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `desempenho`
--
ALTER TABLE `desempenho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `progresso`
--
ALTER TABLE `progresso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `aluno_turma`
--
ALTER TABLE `aluno_turma`
  ADD CONSTRAINT `aluno_turma_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aluno_turma_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `desempenho`
--
ALTER TABLE `desempenho`
  ADD CONSTRAINT `desempenho_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `desempenho_ibfk_2` FOREIGN KEY (`licao_id`) REFERENCES `licoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `progresso`
--
ALTER TABLE `progresso`
  ADD CONSTRAINT `progresso_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
