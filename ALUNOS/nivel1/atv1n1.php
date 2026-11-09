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
            VALUES (:aluno_id, 1, 1, 1)
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>LUMI - Atividade 1 (Nível 1)</title>

    <link rel="icon" type="image/png" href="../img/logo.png" />
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

      <img src="../img/nuvem_parabens.png" class="nuvem-parabens-esq" alt="Nuvem" onerror="this.style.display='none'" />

      <img src="../img/nuvem_parabens.png" class="nuvem-parabens-dir" alt="Nuvem" onerror="this.style.display='none'" />

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

        <img src="../img/lumiminho.png" alt="Lumi" class="mascote-parabens" onerror="this.src='../img/luminho.png'" />
      </div>

      <div class="gramado-parabens"></div>
    </div>
    <a href="nivel1.php" class="btn-voltar" title="Voltar para a Trilha"> ← </a>

    <div class="circulo circulo-azul"></div>
    <div class="circulo circulo-rosa"></div>
    <div class="circulo circulo-verde"></div>
    <div class="circulo circulo-amarelo"></div>

    <div id="etapa1" class="etapa-container etapa-simples">
      <div class="card-letra-wrapper">
        <div class="card-letra">
          <span>Aa</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraA')" title="Ouvir letra A">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa1','etapa2')">➔</button>
    </div>

    <div id="etapa2" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Aa</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraA')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somAbacate')">
            <span>ABACATE</span>

            <img src="../img/abacate.png" alt="Abacate" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somAbacaxi')">
            <span>ABACAXI</span>

            <img src="../img/abacaxi.png" alt="Abacaxi" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somAbelha')">
            <span>ABELHA</span>

            <img src="../img/abelha.png" alt="Abelha" />
          </button>
        </div>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa2','etapa3')">➔</button>
    </div>

    <div id="etapa3" class="etapa-container etapa-simples esconder">
      <div class="card-letra-wrapper">
        <div class="card-letra">
          <span>Bb</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraB')">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa3','etapa4')">➔</button>
    </div>

    <div id="etapa4" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Bb</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraB')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somBaleia')">
            <span>BALEIA</span>

            <img src="../img/baleia.png" alt="Baleia" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somBanana')">
            <span>BANANA</span>

            <img src="../img/banana.png" alt="Banana" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somBola')">
            <span>BOLA</span>

            <img src="../img/bola.png" alt="Bola" />
          </button>
        </div>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa4','etapa5')">➔</button>
    </div>

    <div id="etapa5" class="etapa-container etapa-simples esconder">
      <div class="card-letra-wrapper">
        <div class="card-letra">
          <span>Cc</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraC')">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa5','etapa6')">➔</button>
    </div>

    <div id="etapa6" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Cc</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraC')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somCasa')">
            <span>CASA</span>

            <img src="../img/casa.png" alt="Casa" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somCoelho')">
            <span>COELHO</span>

            <img src="../img/coelho.png" alt="Coelho" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somCoruja')">
            <span>CORUJA</span>

            <img src="../img/coruja.png" alt="Coruja" />
          </button>
        </div>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa6','etapa7')">➔</button>
    </div>

    <div id="etapa7" class="esconder">
      <div class="painel-letras">
        <div
          class="card-letra-arrastavel"
          draggable="true"
          id="letra-A"
          data-letra="a"
          ondragstart="arrastarLetra(event)"
        >
          Aa
        </div>

        <div
          class="card-letra-arrastavel"
          draggable="true"
          id="letra-B"
          data-letra="b"
          ondragstart="arrastarLetra(event)"
        >
          Bb
        </div>

        <div
          class="card-letra-arrastavel"
          draggable="true"
          id="letra-C"
          data-letra="c"
          ondragstart="arrastarLetra(event)"
        >
          Cc
        </div>

        <button class="btn-som" onclick="tocarSom('audioInstrucao')" title="Ouvir instrução">🔊</button>
      </div>

      <div class="grid-palavras-jogo">
        <div
          class="card-palavra-drop"
          data-correta="a"
          data-resto="BELHA"
          data-audio="somAbelha"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_BELHA</span>

          <img src="../img/abelha.png" alt="Abelha" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="a"
          data-resto="BACAXI"
          data-audio="somAbacaxi"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_BACAXI</span>

          <img src="../img/abacaxi.png" alt="Abacaxi" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="a"
          data-resto="BACATE"
          data-audio="somAbacate"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_BACATE</span>

          <img src="../img/abacate.png" alt="Abacate" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="b"
          data-resto="OLA"
          data-audio="somBola"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_OLA</span>

          <img src="../img/bola.png" alt="Bola" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="c"
          data-resto="ORUJA"
          data-audio="somCoruja"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_ORUJA</span>

          <img src="../img/coruja.png" alt="Coruja" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="c"
          data-resto="OELHO"
          data-audio="somCoelho"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_OELHO</span>

          <img src="../img/coelho.png" alt="Coelho" class="imagem-palavra" />
        </div>
      </div>
    </div>

    <audio id="audioIniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>

    <audio id="somLetraA" src="../audios/Letra A.m4a"></audio>

    <audio id="somLetraB" src="../audios/Letra B.m4a"></audio>

    <audio id="somLetraC" src="../audios/Letra C.m4a"></audio>

    <audio id="somAbacate" src="../audios/Abacate.mp3"></audio>

    <audio id="somAbacaxi" src="../audios/Abacaxi.mp3"></audio>

    <audio id="somAbelha" src="../audios/Abelha.mp3"></audio>

    <audio id="somBaleia" src="../audios/Baleia.mp3"></audio>

    <audio id="somBanana" src="../audios/Banana.mp3"></audio>

    <audio id="somBola" src="../audios/Bola.mp3"></audio>

    <audio id="somCasa" src="../audios/Casa.mp3"></audio>

    <audio id="somCoelho" src="../audios/Coelho.mp3"></audio>

    <audio id="somCoruja" src="../audios/Coruja.mp3"></audio>

    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>

    <audio id="somErro" src="../audios/erro.mp3"></audio>

    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>

    <audio id="audioInstrucao" src="../audios/instrucao_arrastar.mp3"></audio>

    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
      <source src="../audios/fundolumii.mp3" type="audio/mpeg" />
    </audio>

    <script>
        setTimeout(() => {
            document.getElementById('tela-transicao').classList.add('transicao-oculta');
        }, 4000);

        function mudarEtapa(etapaAtual, proximaEtapa) {
            document.getElementById(etapaAtual).classList.add('esconder');
            document.getElementById(proximaEtapa).classList.remove('esconder');
            if (proximaEtapa === 'etapa7') {
                tocarSom('audioInstrucao');
                setTimeout(() => {
                    const total = contarFigurasVisiveis();
                    if (total === 0) {
                        const btn = document.getElementById('btn-avancar-etapa6');
                        if (btn) btn.removeAttribute('disabled');
                    } else {
                        acertos = 0;
                        TOTAL_ACERTOS = total;
                    }
                }, 200);
            }
            if (proximaEtapa === 'etapa8') {
                tocarSom('audioInstrucao');
                setTimeout(() => {
                    const total = contarPalavrasVisiveis();
                    if (total === 0) {
                        concluirFase();
                    } else {
                        acertosEtapa6 = 0;
                        TOTAL_ACERTOS_ETAPA6 = total;
                    }
                }, 200);
            }
        }

        function tocarSom(idAudio) {
            const el = document.getElementById(idAudio);
            if (el) {
                el.currentTime = 0;
                el.play();
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

        let acertos = 0;
        const TOTAL_ACERTOS = 6;

        function arrastar(event) {
            event.dataTransfer.setData("text/plain", event.target.id);
        }

        function permitirSoltar(event) {
            event.preventDefault();
            event.currentTarget.classList.add('soltar-hoover');
        }

        function sairDrop(event) {
            event.currentTarget.classList.remove('soltar-hoover');
        }

        function soltar(event) {
            event.preventDefault();
            const dropzone = event.currentTarget;
            dropzone.classList.remove('soltar-hoover');

            const idFigura = event.dataTransfer.getData("text/plain");
            const figura = document.getElementById(idFigura);

            if (!figura) return;

            const letraFigura = figura.getAttribute('data-letra');
            const letraZona = dropzone.getAttribute('data-letra');

            if (letraFigura === letraZona) {
                tocarSom('somAcerto');
                figura.classList.add('concluido');
                figura.setAttribute('draggable', 'false');
                acertos++;

                if (acertos === TOTAL_ACERTOS) {
                    const btnAvancar5 = document.getElementById('btn-avancar-etapa5');
                    if (btnAvancar5) {
                        btnAvancar5.removeAttribute('disabled');
                    }
                }
            } else {
                tocarSom('somErro');
                mostrarErroArraste(dropzone);
            }
        }

        let acertosEtapa6 = 0;
        const TOTAL_ACERTOS_ETAPA6 = 6;

        function arrastarLetra(event) {
            event.dataTransfer.setData("text/plain", event.target.dataset.letra);
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

        function soltarLetra(event) {
            event.preventDefault();
            const dropzone = event.currentTarget;
            dropzone.classList.remove('hover-drop');

            if (dropzone.classList.contains('concluido')) return;

            const letraArrastada = event.dataTransfer.getData("text/plain");
            const letraCorreta = dropzone.dataset.correta;

            if (letraArrastada === letraCorreta) {
                tocarSom('somAcerto');

                const letraMaiuscula = letraCorreta.toUpperCase();
                const restoPalavra = dropzone.dataset.resto;
                const spanTexto = dropzone.querySelector('.texto-palavra');
                
                spanTexto.textContent = letraMaiuscula + restoPalavra;
                dropzone.classList.add('concluido');
                acertosEtapa6++;

                const audioPalavra = dropzone.dataset.audio;
                if (audioPalavra) {
                    setTimeout(() => tocarSom(audioPalavra), 500);
                }

                if (acertosEtapa6 === TOTAL_ACERTOS_ETAPA6) {
                    setTimeout(() => {
                        fetch('atv1n1.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'concluir_fase=1'
                        }).finally(() => {
                            setTimeout(() => {
                                const telaParabens = document.getElementById('tela-parabens');
                                if (telaParabens) {
                                    telaParabens.classList.remove('esconder');
                                    gerarConfetes();
                                    tocarSom('somParabens');
                                }
                                setTimeout(() => {
                                    window.location.href = "nivel1.php";
                                }, 4500);
                            }, 600);
                        });
                    }, 1000);
                }
            } else {
                tocarSom('somErro');
                mostrarErroArraste(dropzone);
            }
        }

        function gerarConfetes() {
            const container = document.getElementById('containerConfetes');
            if (!container) return;

            const cores = ['#ff5252', '#ff7043', '#fbc02d', '#9ccc65', '#26a69a', '#42a5f5', '#7e57c2', '#ec407a'];
            const formatos = ['retangulo', 'circulo', 'serpentina'];

            for (let i = 0; i < 75; i++) {
                const confete = document.createElement('div');
                confete.className = `confete ${formatos[Math.floor(Math.random() * formatos.length)]}`;
                confete.style.backgroundColor = cores[Math.floor(Math.random() * cores.length)];
                confete.style.left = `${Math.random() * 100}%`;
                confete.style.animationDuration = `${2.5 + Math.random() * 3}s`;
                confete.style.animationDelay = `${Math.random() * 2}s`;
                container.appendChild(confete);
            }
        }

        const audioFundo = document.getElementById('somFundo');
        audioFundo.volume = 0.2;
        const tempoSalvo = localStorage.getItem('musica_tempo');
        if (tempoSalvo) audioFundo.currentTime = parseFloat(tempoSalvo);

        audioFundo.play().catch(() => {
            document.addEventListener('click', () => audioFundo.play(), { once: true });
        });

        audioFundo.addEventListener('timeupdate', () => {
            localStorage.setItem('musica_tempo', audioFundo.currentTime);
        });
    </script>
  </body>
</html>