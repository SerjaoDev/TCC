<?php
session_start();
require_once '../conexao.php';

const TOTAL_QUESTOES = 20;
const PAGINA_INICIAL = 'nivel1.php'; // para onde o aluno volta ao terminar a prova

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$aluno_id = $_SESSION['aluno_id'] ?? 1;

/* Recebe a nota enviada por fetch() ao fim da prova e grava no banco */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_prova'])) {
    header('Content-Type: application/json; charset=utf-8');

    $total = TOTAL_QUESTOES;
    $nota = max(0, min((int) ($_POST['nota'] ?? 0), $total));
    $percentual = round(($nota / $total) * 100, 2);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO provas (aluno_id, nota, total, percentual)
             VALUES (:aluno_id, :nota, :total, :percentual)'
        );
        $stmt->execute([
            ':aluno_id'   => $aluno_id,
            ':nota'       => $nota,
            ':total'      => $total,
            ':percentual' => $percentual,
        ]);

        echo json_encode([
            'status'     => 'sucesso',
            'nota'       => $nota,
            'total'      => $total,
            'percentual' => $percentual,
        ]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['status' => 'erro', 'mensagem' => 'Não foi possível salvar a prova.']);
    }
    exit;
}

/* Áudios: o id no HTML é "som-<chave>" e é tocado com tocarSom('<chave>') */
$sons = [
    'acerto'   => 'acerto.mp3',
    'erro'     => 'erro.mp3',
    'parabens' => 'parabens.mp3',
];
foreach (range('A', 'Z') as $letra) {
    $sons["letra$letra"] = "Letra $letra.m4a";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - Prova Final do Alfabeto</title>

    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../CSS/provas.css">
</head>

<body>
    <div class="fundo" aria-hidden="true">
        <div class="bolha bolha1"></div>
        <div class="bolha bolha2"></div>
        <div class="bolha bolha3"></div>
        <div class="bolha bolha4"></div>
    </div>

    <a href="<?= PAGINA_INICIAL ?>" class="btn-voltar" title="Voltar" aria-label="Voltar">←</a>

    <main class="pagina">
        <section class="prova">
            <header class="cabecalho">
                <h1 class="titulo">🏆 Desafio do Alfabeto</h1>
                <p class="subtitulo">Você aprendeu todas as letras! Vamos ver o que você lembra? 🌟</p>
            </header>

            <div class="progresso-area">
                <div class="progresso-info">
                    <span id="numeroQuestao">Questão 1 de <?= TOTAL_QUESTOES ?></span>
                    <span id="pontuacao">⭐ 0</span>
                </div>

                <div class="barra">
                    <div class="barra-progresso" id="barraProgresso"></div>
                </div>
            </div>

            <section class="card-questao" id="cardQuestao">
                <div class="numero-questao" id="tipoQuestao"></div>
                <div class="instrucao" id="instrucao"></div>

                <div id="areaQuestao"></div>

                <div class="feedback" id="feedback"></div>
                <button type="button" class="btn-proxima" id="btnProxima" disabled>Próxima ➜</button>
            </section>
        </section>
    </main>

    <!-- Tela de resultado -->
    <div id="resultado">
        <div id="containerConfetes"></div>

        <div class="resultado-conteudo">
            <h2 class="resultado-titulo" id="resultadoTitulo">PARABÉNS!</h2>

            <img src="../img/Luminho.png" alt="Lumi comemorando" class="mascote"
                onerror="this.onerror=null;this.src='../img/luminho.png'">

            <div class="estrelas" id="resultadoEstrelas">⭐⭐⭐⭐⭐</div>
            <div class="resultado-nota" id="resultadoNota"></div>
            <p class="resultado-mensagem" id="resultadoMensagem"></p>

            <a href="<?= PAGINA_INICIAL ?>" class="btn-final" id="btnTrilha">Voltar para a Trilha 🏠</a>
        </div>
    </div>

    <!-- Áudios -->
    <?php foreach ($sons as $id => $arquivo): ?>
        <audio id="som-<?= $id ?>" src="../audios/<?= htmlspecialchars($arquivo) ?>" preload="auto"></audio>
    <?php endforeach; ?>

    <script>
        /* =====================================================
           Configuração
           ===================================================== */
        const TOTAL_QUESTOES = <?= TOTAL_QUESTOES ?>;
        const PAGINA_INICIAL = '<?= PAGINA_INICIAL ?>';
        const SEGUNDOS_ATE_VOLTAR = 6; // tempo na tela de resultado antes de voltar sozinho

        /* Título e instrução padrão de cada tipo de questão */
        const TIPOS = {
            audio: {
                titulo: '👂 Ouça com atenção!',
                instrucao: 'Qual é a letra que você ouviu?'
            },
            imagem: {
                titulo: '🖼️ Olhe a figura!',
                instrucao: 'Qual é a primeira letra desta palavra?'
            },
            completar: {
                titulo: '🧩 Complete a palavra!',
                instrucao: 'Qual letra está faltando?'
            },
            ordem: {
                titulo: '🔤 Pense no alfabeto!'
            } // a instrução depende da questão
        };

        /* Banco de questões. A letra certa é calculada:
           - audio: campo "letra"
           - imagem e completar: primeira letra da palavra
           - ordem: letra que vem "antes" ou "depois" da referência
           A letra certa precisa estar em "alternativas". */
        const BANCO_QUESTOES = [
            // Áudio
            {
                tipo: 'audio',
                letra: 'A',
                alternativas: ['A', 'B', 'D', 'P']
            },
            {
                tipo: 'audio',
                letra: 'M',
                alternativas: ['N', 'M', 'W', 'A']
            },
            {
                tipo: 'audio',
                letra: 'S',
                alternativas: ['C', 'S', 'Z', 'F']
            },
            {
                tipo: 'audio',
                letra: 'R',
                alternativas: ['P', 'R', 'B', 'D']
            },
            {
                tipo: 'audio',
                letra: 'T',
                alternativas: ['D', 'P', 'T', 'F']
            },

            // Primeira letra da figura
            {
                tipo: 'imagem',
                palavra: 'ABELHA',
                imagem: '../img/abelha.png',
                alternativas: ['A', 'E', 'B', 'M']
            },
            {
                tipo: 'imagem',
                palavra: 'BOLA',
                imagem: '../img/bola.png',
                alternativas: ['D', 'P', 'B', 'A']
            },
            {
                tipo: 'imagem',
                palavra: 'COELHO',
                imagem: '../img/coelho.png',
                alternativas: ['G', 'C', 'Q', 'S']
            },
            {
                tipo: 'imagem',
                palavra: 'MACACO',
                imagem: '../img/Macaco.png',
                alternativas: ['N', 'M', 'W', 'B']
            },
            {
                tipo: 'imagem',
                palavra: 'OVELHA',
                imagem: '../img/Ovelha.png',
                alternativas: ['C', 'O', 'Q', 'D']
            },

            // Completar a palavra
            {
                tipo: 'completar',
                palavra: 'BANANA',
                imagem: '../img/banana.png',
                alternativas: ['A', 'B', 'M', 'P']
            },
            {
                tipo: 'completar',
                palavra: 'ABACATE',
                imagem: '../img/abacate.png',
                alternativas: ['A', 'B', 'M', 'P']
            },
            {
                tipo: 'completar',
                palavra: 'BALEIA',
                imagem: '../img/baleia.png',
                alternativas: ['B', 'D', 'P', 'V']
            },
            {
                tipo: 'completar',
                palavra: 'BOLA',
                imagem: '../img/bola.png',
                alternativas: ['A', 'B', 'D', 'P']
            },
            {
                tipo: 'completar',
                palavra: 'GATO',
                imagem: '../img/Gato.png',
                alternativas: ['C', 'G', 'P', 'R']
            },

            // Ordem do alfabeto
            {
                tipo: 'ordem',
                posicao: 'depois',
                referencia: 'A',
                alternativas: ['B', 'C', 'D', 'E']
            },
            {
                tipo: 'ordem',
                posicao: 'depois',
                referencia: 'F',
                alternativas: ['E', 'F', 'G', 'H']
            },
            {
                tipo: 'ordem',
                posicao: 'antes',
                referencia: 'M',
                alternativas: ['J', 'K', 'L', 'N']
            },
            {
                tipo: 'ordem',
                posicao: 'depois',
                referencia: 'R',
                alternativas: ['P', 'Q', 'S', 'T']
            },
            {
                tipo: 'ordem',
                posicao: 'antes',
                referencia: 'Z',
                alternativas: ['W', 'X', 'Y', 'Z']
            }
        ];

        /* Resultado final: vale o primeiro nível cujo "minimo" a nota alcança */
        const EXPLORADOR = {
            titulo: '🌱 PEQUENO EXPLORADOR!',
            mensagem: 'Você está aprendendo! Continue praticando com o Lumi. Cada tentativa ajuda você a aprender mais! 💛'
        };

        const NIVEIS_RESULTADO = [{
                minimo: 18,
                estrelas: '⭐⭐⭐⭐⭐',
                comemorar: true,
                titulo: '🏆 MESTRE DO ALFABETO!',
                mensagem: 'Você conhece muito bem as letras! O Lumi está muito orgulhoso de você! 🌟'
            },
            {
                minimo: 14,
                estrelas: '⭐⭐⭐⭐',
                comemorar: true,
                titulo: '🌈 AVENTUREIRO DO ALFABETO!',
                mensagem: 'Muito bem! Você já aprendeu muitas letras. Continue praticando e você ficará ainda melhor! ⭐'
            },
            {
                minimo: 10,
                estrelas: '⭐⭐⭐',
                ...EXPLORADOR
            },
            {
                minimo: 5,
                estrelas: '⭐⭐',
                ...EXPLORADOR
            },
            {
                minimo: 0,
                estrelas: '⭐',
                ...EXPLORADOR
            }
        ];

        const MENSAGENS_ACERTO = ['🎉 Muito bem!', '⭐ Isso! Você acertou!', '👏 Parabéns!', '🌟 Muito bom!', '🥳 Acertou!'];
        const MENSAGENS_ERRO = ['💛 Tudo bem! Vamos continuar!', '🌱 Vamos para a próxima!', '😊 Não tem problema!', '💪 Continue tentando!'];

        const ICONE_ALTO_FALANTE = `
            <svg viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>`;

        /* =====================================================
           Estado e elementos da página
           ===================================================== */
        let questaoAtual = 0;
        let pontuacao = 0;
        let respondeu = false;

        const $ = id => document.getElementById(id);

        const numeroQuestao = $('numeroQuestao');
        const pontuacaoElemento = $('pontuacao');
        const barraProgresso = $('barraProgresso');
        const tipoQuestao = $('tipoQuestao');
        const instrucao = $('instrucao');
        const areaQuestao = $('areaQuestao');
        const feedback = $('feedback');
        const btnProxima = $('btnProxima');
        const resultado = $('resultado');
        const btnTrilha = $('btnTrilha');

        /* =====================================================
           Utilitários
           ===================================================== */
        const sortear = lista => lista[Math.floor(Math.random() * lista.length)];
        const esperar = ms => new Promise(resolve => setTimeout(resolve, ms));

        function embaralhar(lista) {
            const copia = [...lista];
            for (let i = copia.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [copia[i], copia[j]] = [copia[j], copia[i]];
            }
            return copia;
        }

        function criar(tag, classe = '', texto = '') {
            const elemento = document.createElement(tag);
            if (classe) elemento.className = classe;
            if (texto) elemento.textContent = texto;
            return elemento;
        }

        function tocarSom(nome) {
            const audio = $('som-' + nome);
            if (!audio) return;

            audio.currentTime = 0;
            audio.play().catch(() => {});
        }

        const tocarLetra = letra => tocarSom('letra' + letra.toUpperCase());

        /* =====================================================
           Montagem da prova
           ===================================================== */
        function prepararQuestao(q) {
            let {
                instrucao
            } = TIPOS[q.tipo];
            const {
                titulo
            } = TIPOS[q.tipo];
            let letra = q.letra;
            let resto = '';

            if (q.tipo === 'ordem') {
                const deslocamento = q.posicao === 'depois' ? 1 : -1;
                letra = String.fromCharCode(q.referencia.charCodeAt(0) + deslocamento);
                instrucao = `Qual letra vem ${q.posicao} do ${q.referencia}?`;
            } else if (q.tipo === 'imagem' || q.tipo === 'completar') {
                letra = q.palavra[0];
                resto = q.palavra.slice(1);
            }

            if (!q.alternativas.includes(letra)) {
                console.warn('Questão sem a resposta certa nas alternativas:', q);
            }

            return {
                ...q,
                titulo,
                instrucao,
                letra,
                resto,
                alternativas: embaralhar(q.alternativas)
            };
        }

        const prova = embaralhar(BANCO_QUESTOES)
            .slice(0, TOTAL_QUESTOES)
            .map(prepararQuestao);

        /* =====================================================
           Exibição das questões
           ===================================================== */
        function criarImagem(src, alt) {
            const img = criar('img', 'imagem-questao');
            img.src = src;
            img.alt = alt;
            return img;
        }

        /* Parte de cima de cada tipo de questão (as alternativas vêm depois) */
        const desenharQuestao = {
            audio(q) {
                const botao = criar('button', 'btn-audio');
                botao.type = 'button';
                botao.title = 'Ouvir novamente';
                botao.innerHTML = ICONE_ALTO_FALANTE;
                botao.addEventListener('click', () => tocarLetra(q.letra));

                const dica = criar('div', '', 'Clique no alto-falante para ouvir novamente');
                dica.style.cssText = 'font-size: 20px; font-weight: bold; margin-bottom: 15px;';

                areaQuestao.append(botao, dica);
            },

            imagem(q) {
                areaQuestao.append(
                    criarImagem(q.imagem, q.palavra),
                    criar('div', 'palavra-grande', q.palavra)
                );
            },

            completar(q) {
                const palavra = criar('div', 'palavra-grande');
                palavra.append(criar('span', 'lacuna', '_'), q.resto);

                areaQuestao.append(criarImagem(q.imagem, 'Imagem da palavra'), palavra);
            },

            ordem() {
                areaQuestao.append(criar('div', 'letra-grande', '?'));
            }
        };

        function criarAlternativas(q) {
            const container = criar('div', 'alternativas');

            q.alternativas.forEach(letra => {
                const botao = criar('button', 'alternativa', letra);
                botao.type = 'button';
                botao.addEventListener('click', () => responder(letra, q, botao));
                container.append(botao);
            });

            return container;
        }

        function mostrarQuestao() {
            respondeu = false;
            feedback.textContent = '';
            feedback.className = 'feedback';
            btnProxima.disabled = true;

            const q = prova[questaoAtual];

            numeroQuestao.textContent = `Questão ${questaoAtual + 1} de ${TOTAL_QUESTOES}`;
            pontuacaoElemento.textContent = `⭐ ${pontuacao}`;
            barraProgresso.style.width = `${(questaoAtual / TOTAL_QUESTOES) * 100}%`;
            tipoQuestao.textContent = q.titulo;
            instrucao.textContent = q.instrucao;

            areaQuestao.replaceChildren();
            desenharQuestao[q.tipo](q);
            areaQuestao.append(criarAlternativas(q));
        }

        /* =====================================================
           Respostas
           ===================================================== */
        function responder(resposta, q, botaoClicado) {
            if (respondeu) return;
            respondeu = true;

            const botoes = areaQuestao.querySelectorAll('.alternativa');
            botoes.forEach(botao => botao.disabled = true);

            if (resposta === q.letra) {
                pontuacao++;
                botaoClicado.classList.add('correta');
                feedback.textContent = sortear(MENSAGENS_ACERTO);
                feedback.classList.add('acerto');
                tocarSom('acerto');
            } else {
                botaoClicado.classList.add('errada');
                botoes.forEach(botao => {
                    if (botao.textContent === q.letra) botao.classList.add('correta');
                });
                feedback.textContent = sortear(MENSAGENS_ERRO);
                feedback.classList.add('erro');
                tocarSom('erro');
            }

            pontuacaoElemento.textContent = `⭐ ${pontuacao}`;
            btnProxima.disabled = false;
        }

        btnProxima.addEventListener('click', () => {
            if (!respondeu) return;

            questaoAtual++;

            if (questaoAtual >= TOTAL_QUESTOES) {
                finalizarProva();
            } else {
                mostrarQuestao();
            }
        });

        /* =====================================================
           Fim da prova: salva, mostra o resultado e volta à trilha
           ===================================================== */
        function salvarResultado() {
            const dados = new URLSearchParams({
                salvar_prova: '1',
                nota: pontuacao,
                total: TOTAL_QUESTOES
            });

            return fetch('prova.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: dados.toString()
                })
                .catch(() => {}); // mesmo sem salvar, o aluno vê o resultado
        }

        async function finalizarProva() {
            barraProgresso.style.width = '100%';

            const salvando = salvarResultado();

            await esperar(500);
            mostrarResultado();

            await contagemRegressiva(SEGUNDOS_ATE_VOLTAR);

            // garante que a nota foi gravada antes de sair (espera no máximo mais 4s)
            await Promise.race([salvando, esperar(4000)]);

            window.location.href = PAGINA_INICIAL;
        }

        async function contagemRegressiva(segundos) {
            for (let restante = segundos; restante > 0; restante--) {
                btnTrilha.textContent = `Voltar para a Trilha 🏠 (${restante})`;
                await esperar(1000);
            }
            btnTrilha.textContent = 'Voltando… 🏠';
        }

        function mostrarResultado() {
            const nivel = NIVEIS_RESULTADO.find(n => pontuacao >= n.minimo);

            resultado.classList.add('mostrar');

            $('resultadoEstrelas').textContent = nivel.estrelas;
            $('resultadoNota').textContent = `Você acertou ${pontuacao} de ${TOTAL_QUESTOES}!`;
            $('resultadoTitulo').textContent = nivel.titulo;
            $('resultadoMensagem').textContent = nivel.mensagem;

            if (nivel.comemorar) tocarSom('parabens');
            gerarConfetes();
        }

        function gerarConfetes() {
            const cores = ['#ff5252', '#ff7043', '#fbc02d', '#9ccc65', '#26a69a', '#42a5f5', '#7e57c2', '#ec407a'];
            const container = $('containerConfetes');

            for (let i = 0; i < 80; i++) {
                const confete = criar('div', 'confete');
                Object.assign(confete.style, {
                    backgroundColor: sortear(cores),
                    left: `${Math.random() * 100}%`,
                    animationDuration: `${2.5 + Math.random() * 3}s`,
                    animationDelay: `${Math.random() * 1.5}s`
                });
                container.append(confete);
            }
        }

        /* =====================================================
           Início
           ===================================================== */
        mostrarQuestao();
    </script>
</body>

</html>