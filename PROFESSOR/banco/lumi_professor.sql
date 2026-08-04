CREATE TABLE professores (
  id SERIAL PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  foto VARCHAR(255) DEFAULT 'padrao.png',
  data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO professores (id, nome, email, senha, foto, data_cadastro) VALUES
(1, 'felipe', 'felipe3@gmail.com', '$2y$10$kBwG1J6qWj.gJ8pyVPRL1Oc8NUF48I4b0jje9hljBTLbKJr4hRPhK', 'padrao.png', '2026-07-30 00:54:08');

SELECT setval('professores_id_seq', (SELECT MAX(id) FROM professores));

CREATE TABLE turmas (
  id SERIAL PRIMARY KEY,
  professor_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT DEFAULT NULL,
  CONSTRAINT turmas_ibfk_1 FOREIGN KEY (professor_id) REFERENCES professores (id) ON DELETE CASCADE
);

INSERT INTO turmas (id, professor_id, nome, descricao) VALUES
(5, 1, '1ºA', NULL),
(6, 1, '1ºC', NULL);

SELECT setval('turmas_id_seq', (SELECT MAX(id) FROM turmas));

CREATE TABLE alunos (
  id SERIAL PRIMARY KEY,
  professor_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  usuario VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  foto VARCHAR(255) DEFAULT 'padrao.png',
  data_nascimento DATE DEFAULT NULL,
  turma_id INT DEFAULT NULL,
  turma VARCHAR(100) DEFAULT NULL,
  data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  senha_visivel VARCHAR(255) DEFAULT NULL,
  CONSTRAINT alunos_ibfk_1 FOREIGN KEY (professor_id) REFERENCES professores (id) ON DELETE CASCADE
);

INSERT INTO alunos (id, professor_id, nome, usuario, senha, foto, data_nascimento, turma_id, turma, data_cadastro, senha_visivel) VALUES
(13, 1, 'Felipe', 'Sec', '$2y$10$SAjSTbRANNLV8oa0XNx1P.HA4qVZYFLwN4qi1xzDQ4ciZi6FsxqAC', 'padrao.png', '2020-08-01', 5, NULL, '2026-07-30 06:47:18', 'Sec01');

SELECT setval('alunos_id_seq', (SELECT MAX(id) FROM alunos));

CREATE TABLE aluno_turma (
  id SERIAL PRIMARY KEY,
  aluno_id INT NOT NULL,
  turma_id INT NOT NULL,
  CONSTRAINT aluno_turma_ibfk_1 FOREIGN KEY (aluno_id) REFERENCES alunos (id) ON DELETE CASCADE,
  CONSTRAINT aluno_turma_ibfk_2 FOREIGN KEY (turma_id) REFERENCES turmas (id) ON DELETE CASCADE
);

CREATE TABLE licoes (
  id SERIAL PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT DEFAULT NULL,
  nivel INT DEFAULT 1,
  categoria VARCHAR(100) DEFAULT NULL,
  data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE desempenho (
  id SERIAL PRIMARY KEY,
  aluno_id INT NOT NULL,
  licao_id INT NOT NULL,
  resultado VARCHAR(50) DEFAULT NULL,
  pontuacao INT DEFAULT 0,
  tempo_gasto INT DEFAULT 0,
  data_realizacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT desempenho_ibfk_1 FOREIGN KEY (aluno_id) REFERENCES alunos (id) ON DELETE CASCADE,
  CONSTRAINT desempenho_ibfk_2 FOREIGN KEY (licao_id) REFERENCES licoes (id) ON DELETE CASCADE
);

CREATE TABLE notificacoes (
  id SERIAL PRIMARY KEY,
  professor_id INT NOT NULL,
  mensagem TEXT DEFAULT NULL,
  visualizada SMALLINT DEFAULT 0, 
  data TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT notificacoes_ibfk_1 FOREIGN KEY (professor_id) REFERENCES professores (id) ON DELETE CASCADE
);

CREATE TABLE progresso (
  id SERIAL PRIMARY KEY,
  aluno_id INT NOT NULL,
  nivel_atual INT DEFAULT 1,
  estrelas INT DEFAULT 0,
  moedas INT DEFAULT 0,
  licoes_concluidas INT DEFAULT 0,
  acertos INT DEFAULT 0,
  erros INT DEFAULT 0,
  tempo_estudo INT DEFAULT 0,
  ultimo_acesso TIMESTAMP DEFAULT NULL,
  CONSTRAINT progresso_ibfk_1 FOREIGN KEY (aluno_id) REFERENCES alunos (id) ON DELETE CASCADE
);

INSERT INTO progresso (id, aluno_id, nivel_atual, estrelas, moedas, licoes_concluidas, acertos, erros, tempo_estudo, ultimo_acesso) VALUES
(6, 13, 1, 0, 0, 0, 0, 0, 0, NULL);

SELECT setval('progresso_id_seq', (SELECT MAX(id) FROM progresso));