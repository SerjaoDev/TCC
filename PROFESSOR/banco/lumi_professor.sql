CREATE TABLE professores (
id SERIAL PRIMARY KEY,
nome VARCHAR(150) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
senha VARCHAR(255) NOT NULL,
foto VARCHAR(255) DEFAULT 'padrao.png',
data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE turmas (
id SERIAL PRIMARY KEY,
professor_id INTEGER NOT NULL,
nome VARCHAR(100) NOT NULL,
descricao TEXT,
CONSTRAINT fk_turma_professor FOREIGN KEY(professor_id) REFERENCES professores(id) ON DELETE CASCADE
);

CREATE TABLE alunos (
id SERIAL PRIMARY KEY,
professor_id INTEGER NOT NULL,
nome VARCHAR(150) NOT NULL,
usuario VARCHAR(100) UNIQUE NOT NULL,
senha VARCHAR(255) NOT NULL,
senha_inicial VARCHAR(255),
foto VARCHAR(255) DEFAULT 'padrao.png',
data_nascimento DATE,
turma_id INTEGER,
data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT fk_aluno_professor FOREIGN KEY(professor_id) REFERENCES professores(id) ON DELETE CASCADE,
CONSTRAINT fk_aluno_turma FOREIGN KEY(turma_id) REFERENCES turmas(id) ON DELETE SET NULL
);

CREATE TABLE licoes (
id SERIAL PRIMARY KEY,
titulo VARCHAR(150) NOT NULL,
descricao TEXT,
nivel INTEGER DEFAULT 1,
categoria VARCHAR(100),
data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE progresso (
id SERIAL PRIMARY KEY,
aluno_id INTEGER UNIQUE NOT NULL,
nivel_atual INTEGER DEFAULT 1,
estrelas INTEGER DEFAULT 0,
moedas INTEGER DEFAULT 0,
licoes_concluidas INTEGER DEFAULT 0,
acertos INTEGER DEFAULT 0,
erros INTEGER DEFAULT 0,
tempo_estudo INTEGER DEFAULT 0,
ultimo_acesso TIMESTAMP,
CONSTRAINT fk_progresso_aluno FOREIGN KEY(aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
);

CREATE TABLE desempenho (
id SERIAL PRIMARY KEY,
aluno_id INTEGER NOT NULL,
licao_id INTEGER NOT NULL,
resultado VARCHAR(50),
pontuacao INTEGER DEFAULT 0,
tempo_gasto INTEGER DEFAULT 0,
data_realizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT fk_desempenho_aluno FOREIGN KEY(aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
CONSTRAINT fk_desempenho_licao FOREIGN KEY(licao_id) REFERENCES licoes(id) ON DELETE CASCADE
);

CREATE TABLE notificacoes (
id SERIAL PRIMARY KEY,
professor_id INTEGER NOT NULL,
mensagem TEXT,
visualizada BOOLEAN DEFAULT FALSE,
data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT fk_notificacao_professor FOREIGN KEY(professor_id) REFERENCES professores(id) ON DELETE CASCADE
);

CREATE INDEX idx_alunos_usuario ON alunos(usuario);
CREATE INDEX idx_alunos_turma ON alunos(turma_id);
CREATE INDEX idx_desempenho_aluno ON desempenho(aluno_id);