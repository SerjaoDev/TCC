<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_fase'])) {
    header('Content-Type: application/json; charset=utf-8');

    $aluno_id = $_SESSION['aluno_id'] ?? 1;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO progresso (
                aluno_id,
                estrutura_id,
                nivel_atual,
                licoes_concluidas
            )
            VALUES (:aluno_id, 1, 7, 1)
            ON DUPLICATE KEY UPDATE
                licoes_concluidas = licoes_concluidas + 1
        ");

        $stmt->execute([
            ':aluno_id' => $aluno_id
        ]);

        echo json_encode([
            'status' => 'sucesso',
            'aluno_id' => $aluno_id
        ]);
    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            'status' => 'erro',
            'mensagem' => $e->getMessage()
        ]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LUMI - Atividade 7 (Nível 1)</title>

    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../CSS/nivel1.css">

    <script src="https://cdn.jsdelivr.net/npm/drag-drop-touch-polyfill@1.0.2/DragDropTouch.js"></script>
</head>

<body>
    <div id="tela-transicao">
        <div class="nuvem-container">
            <div class="nuvem nuvem1"></div>
            <div class="nuvem nuvem2"></div>
            <div class="nuvem nuvem3"></div>
        </div>
    </div>

    <div id="tela-parabens" class="esconder">
        <div class="container-confetes" id="containerConfetes"></div>

        <img src="../img/nuvem_parabens.png" class="nuvem-parabens-esq" alt="Nuvem" onerror="this.style.display='none'">

        <img src="../img/nuvem_parabens.png" class="nuvem-parabens-dir" alt="Nuvem" onerror="this.style.display='none'">

        <div class="conteudo-parabens-centro">
            <div class="texto-parabens-colorido">
                <span class="char-p1">P</span>
                <span class="char-a1">A</span>
                <span class="char-r">R</span>
                <span class="char-a2">A</span>
                <span class="char-b">B</span>
                <span class="char-e">É</span>
                <span class="char-s1">N</span>
                <span class="char-s2">S</span>
            </div>

            <img src="../img/lumiminho.png" alt="Lumi" class="mascote-parabens" onerror="this.src='../img/luminho.png'">
        </div>

        <div class="gramado-parabens"></div>
    </div>

    <a href="nivel1.php" class="btn-voltar" title="Voltar para a Trilha">←</a>

    <div class="circulo circulo-azul"></div>
    <div class="circulo circulo-rosa"></div>
    <div class="circulo circulo-verde"></div>
    <div class="circulo circulo-amarelo"></div>

    <div id="etapa1" class="etapa-container etapa-simples">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Pp</span>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraP')" title="Ouvir letra P">🔊</button>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

    <div id="etapa2" class="etapa-container esconder">
        <div class="conteudo-etapa">
            <div class="card-letra-wrapper">
                <div class="card-letra">
                    <span>Pp</span>
                </div>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraP')">🔊</button>

            <div class="lista-palavras">
                <button class="btn-palavra" onclick="tocarSom('somPato')">
                    <span>PATO</span>
                    <img src="../img/Pato.png" alt="Pato">
                </button>

                <button class="btn-palavra" onclick="tocarSom('somPipa')">
                    <span>PIPA</span>
                    <img src="../img/Pipa.png" alt="Pipa">
                </button>

                <button class="btn-palavra" onclick="tocarSom('somPanela')">
                    <span>PANELA</span>
                    <img src="../img/Panela.png" alt="Panela">
                </button>
            </div>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa2', 'etapa3')">➔</button>
    </div>

    <div id="etapa3" class="etapa-container etapa-simples esconder">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Qq</span>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraQ')">🔊</button>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa3', 'etapa4')">➔</button>
    </div>

    <div id="etapa4" class="etapa-container esconder">
        <div class="conteudo-etapa">
            <div class="card-letra-wrapper">
                <div class="card-letra">
                    <span>Qq</span>
                </div>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraQ')">🔊</button>

            <div class="lista-palavras">
                <button class="btn-palavra" onclick="tocarSom('somQueijo')">
                    <span>QUEIJO</span>
                    <img src="../img/Queijo.png" alt="Queijo">
                </button>

                <button class="btn-palavra" onclick="tocarSom('somQuadro')">
                    <span>QUADRO</span>
                    <img src="../img/Quadro.png" alt="Quadro">
                </button>
            </div>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa4', 'etapa5')">➔</button>
    </div>

    <div id="etapa5" class="esconder">
        <div class="painel-letras">
            <div class="card-letra-arrastavel" draggable="true" id="letra-P" data-letra="p"
                ondragstart="arrastarLetra(event)">Pp</div>

            <div class="card-letra-arrastavel" draggable="true" id="letra-Q" data-letra="q"
                ondragstart="arrastarLetra(event)">Qq</div>

            <button class="btn-som" onclick="tocarSom('audioInstrucao')" title="Ouvir instrução">🔊</button>
        </div>

        <div class="grid-palavras-jogo">
            <div class="card-palavra-drop" data-correta="p" data-resto="ATO" data-audio="somPato"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_ATO</span>
                <img src="../img/Pato.png" alt="Pato" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="p" data-resto="IPA" data-audio="somPipa"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_IPA</span>
                <img src="../img/Pipa.png" alt="Pipa" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="q" data-resto="UEIJO" data-audio="somQueijo"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_UEIJO</span>
                <img src="../img/Queijo.png" alt="Queijo" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="q" data-resto="UADRO" data-audio="somQuadro"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_UADRO</span>
                <img src="../img/Quadro.png" alt="Quadro" class="imagem-palavra">
            </div>
        </div>
    </div>

    <audio id="audioIniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>

    <audio id="somLetraP" src="../audios/Letra P.m4a"></audio>
    <audio id="somLetraQ" src="../audios/Letra Q.m4a"></audio>
    <audio id="somLetraR" src="../audios/Letra R.m4a"></audio>

    <audio id="somPato" src="../audios/Pato.mp3"></audio>
    <audio id="somPipa" src="../audios/Pipa.mp3"></audio>
    <audio id="somPanela" src="../audios/Panela.mp3"></audio>

    <audio id="somQueijo" src="../audios/Queijo.mp3"></audio>
    <audio id="somQuadro" src="../audios/Quadro.mp3"></audio>

    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>
    <audio id="somErro" src="../audios/erro.mp3"></audio>
    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>
    <audio id="audioInstrucao" src="../audios/instrucao_arrastar.mp3"></audio>
    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
        <source src="../audios/fundolumii.mp3" type="audio/mpeg">
    </audio>

    <script>
        setTimeout(() => {
            document.getElementById('tela-transicao')
                .classList.add('transicao-oculta');
        }, 4000);

        function mudarEtapa(etapaAtual, proximaEtapa) {
            document.getElementById(etapaAtual)
                .classList.add('esconder');

            document.getElementById(proximaEtapa)
                .classList.remove('esconder');

            if (proximaEtapa === 'etapa7') {
                tocarSom('audioInstrucao');

                acertosEtapa7 = 0;

                document.querySelectorAll('.card-palavra-drop')
                    .forEach(card => {
                        card.classList.remove('concluido');
                        card.querySelector('.texto-palavra').textContent =
                            '_' + card.dataset.resto;
                    });
            }
        }

        function tocarSom(idAudio) {
            const el = document.getElementById(idAudio);

            if (el) {
                el.currentTime = 0;

                el.play().catch(() => { });
            }
        }

        function mostrarErroArraste(elemento) {
            if (!elemento) return;

            elemento.classList.remove('erro-arraste');

            void elemento.offsetWidth;

            elemento.classList.add('erro-arraste');

            tocarSom('audioTentarNovamente');

            setTimeout(() => {
                elemento.classList.remove('erro-arraste');
            }, 1500);
        }

        function arrastarLetra(event) {
            event.dataTransfer.setData(
                'text/plain',
                event.currentTarget.dataset.letra
            );
        }

        function permitirSoltarLetra(event) {
            event.preventDefault();

            const dropzone = event.currentTarget;

            if (!dropzone.classList.contains('concluido')) {
                dropzone.classList.add('hover-drop');
            }
        }

        function sairDropLetra(event) {
            event.currentTarget.classList.remove('hover-drop');
        }

        let acertosEtapa7 = 0;

        const TOTAL_ACERTOS_ETAPA7 = 4;

        let faseConcluida = false;

        function soltarLetra(event) {
            event.preventDefault();

            const dropzone = event.currentTarget;

            dropzone.classList.remove('hover-drop');

            if (dropzone.classList.contains('concluido')) {
                return;
            }

            const letraArrastada =
                event.dataTransfer.getData('text/plain').toLowerCase();

            const letraCorreta = dropzone.dataset.correta;

            if (letraArrastada === letraCorreta) {
                tocarSom('somAcerto');

                const letraMaiuscula = letraCorreta.toUpperCase();

                const restoPalavra = dropzone.dataset.resto;

                const spanTexto =
                    dropzone.querySelector('.texto-palavra');

                spanTexto.textContent = letraMaiuscula + restoPalavra;

                dropzone.classList.add('concluido');

                acertosEtapa7++;

                const audioPalavra = dropzone.dataset.audio;

                if (audioPalavra) {
                    setTimeout(() => {
                        tocarSom(audioPalavra);
                    }, 500);
                }

                if (
                    acertosEtapa7 === TOTAL_ACERTOS_ETAPA7 &&
                    !faseConcluida
                ) {
                    faseConcluida = true;

                    setTimeout(() => {
                        concluirFase();
                    }, 1000);
                }
            } else {
                tocarSom('somErro');

                mostrarErroArraste(dropzone);
            }
        }

        async function concluirFase() {
            try {
                const resposta = await fetch('atv7n1.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type':
                            'application/x-www-form-urlencoded'
                    },
                    body: 'concluir_fase=7'
                });

                if (!resposta.ok) {
                    throw new Error('Não foi possível registrar o progresso.');
                }

                const resultado = await resposta.json();

                if (resultado.status !== 'sucesso') {
                    throw new Error(
                        resultado.mensagem || 'Erro ao registrar progresso.'
                    );
                }
            } catch (erro) {
                console.error('Erro ao concluir a fase:', erro);
            }

            const telaParabens =
                document.getElementById('tela-parabens');

            if (telaParabens) {
                telaParabens.classList.remove('esconder');

                gerarConfetes();

                tocarSom('somParabens');
            }

            setTimeout(() => {
                window.location.href = 'nivel1.php';
            }, 4500);
        }

        function gerarConfetes() {
            const container =
                document.getElementById('containerConfetes');

            if (!container) return;

            container.innerHTML = '';

            const cores = [
                '#ff5252',
                '#ff7043',
                '#fbc02d',
                '#9ccc65',
                '#26a69a',
                '#42a5f5',
                '#7e57c2',
                '#ec407a'
            ];

            const formatos = [
                'retangulo',
                'circulo',
                'serpentina'
            ];

            for (let i = 0; i < 75; i++) {
                const confete = document.createElement('div');

                confete.className =
                    'confete ' +
                    formatos[
                    Math.floor(Math.random() * formatos.length)
                    ];

                confete.style.backgroundColor =
                    cores[Math.floor(Math.random() * cores.length)];

                confete.style.left = `${Math.random() * 100}%`;

                confete.style.animationDuration =
                    `${2.5 + Math.random() * 3}s`;

                confete.style.animationDelay =
                    `${Math.random() * 2}s`;

                container.appendChild(confete);
            }
        }

        const audioFundo = document.getElementById('somFundo');

        audioFundo.volume = 0.2;

        const tempoSalvo = localStorage.getItem('musica_tempo');

        if (tempoSalvo) {
            audioFundo.currentTime = parseFloat(tempoSalvo);
        }

        audioFundo.play().catch(() => {
            document.addEventListener(
                'click',
                () => audioFundo.play().catch(() => { }),
                { once: true }
            );
        });

        audioFundo.addEventListener('timeupdate', () => {
            localStorage.setItem(
                'musica_tempo',
                audioFundo.currentTime
            );
        });
    </script>
</body>

</html>