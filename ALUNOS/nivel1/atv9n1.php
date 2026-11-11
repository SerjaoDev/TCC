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
            VALUES (:aluno_id, 1, 9, 1)
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

    <title>LUMI - Atividade 9 (Nível 1)</title>

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
          <span>Tt</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraT')" title="Ouvir letra T">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa1','etapa2')">➔</button>
    </div>

    <div id="etapa2" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Tt</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraT')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somTatu')">
            <span>TATU</span>

            <img src="../img/tatu.png" alt="Tatu" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somTapete')">
            <span>TAPETE</span>

            <img src="../img/tapete.png" alt="Tapete" />
          </button>
        </div>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa2','etapa3')">➔</button>
    </div>

    <div id="etapa3" class="etapa-container etapa-simples esconder">
      <div class="card-letra-wrapper">
        <div class="card-letra">
          <span>Uu</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraU')">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa3','etapa4')">➔</button>
    </div>

    <div id="etapa4" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Uu</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraU')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somUva')">
            <span>UVA</span>

            <img src="../img/uva.png" alt="Uva" />
          </button>
        </div>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somUrso')">
            <span>URSO</span>

            <img src="../img/urso.png" alt="Urso" />
          </button>
        </div>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa4','etapa5')">➔</button>
    </div>

    <div id="etapa5" class="etapa-container etapa-simples esconder">
      <div class="card-letra-wrapper">
        <div class="card-letra">
          <span>Vv</span>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraV')">🔊</button>
      </div>

      <button class="btn-avancar" onclick="mudarEtapa('etapa5','etapa6')">➔</button>
    </div>

    <div id="etapa6" class="etapa-container esconder">
      <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
          <div class="card-letra">
            <span>Vv</span>
          </div>
        </div>

        <button class="btn-som" onclick="tocarSom('somLetraV')">🔊</button>

        <div class="lista-palavras">
          <button class="btn-palavra" onclick="tocarSom('somVaca')">
            <span>VACA</span>

            <img src="../img/vaca.png" alt="Vaca" />
          </button>

          <button class="btn-palavra" onclick="tocarSom('somVassoura')">
            <span>VASSOURA</span>

            <img src="../img/vassoura.png" alt="Vassoura" />
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
          id="letra-T"
          data-letra="t"
          ondragstart="arrastarLetra(event)"
        >
          Tt
        </div>

        <div
          class="card-letra-arrastavel"
          draggable="true"
          id="letra-U"
          data-letra="u"
          ondragstart="arrastarLetra(event)"
        >
          Uu
        </div>

        <div
          class="card-letra-arrastavel"
          draggable="true"
          id="letra-V"
          data-letra="v"
          ondragstart="arrastarLetra(event)"
        >
          Vv
        </div>

        <button class="btn-som" onclick="tocarSom('audioInstrucao')" title="Ouvir instrução">🔊</button>
      </div>

      <div class="grid-palavras-jogo">
        <div
          class="card-palavra-drop"
          data-correta="t"
          data-resto="ATU"
          data-audio="somTatu"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_ATU</span>

          <img src="../img/tatu.png" alt="Tatu" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="t"
          data-resto="APETE"
          data-audio="somTapete"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_APETE</span>

          <img src="../img/tapete.png" alt="Tapete" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="u"
          data-resto="VA"
          data-audio="somUva"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_VA</span>

          <img src="../img/uva.png" alt="Uva" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="u"
          data-resto="RSO"
          data-audio="somUrso"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_RSO</span>

          <img src="../img/urso.png" alt="Urso" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="v"
          data-resto="ACA"
          data-audio="somVaca"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_ACA</span>

          <img src="../img/vaca.png" alt="Vaca" class="imagem-palavra" />
        </div>

        <div
          class="card-palavra-drop"
          data-correta="v"
          data-resto="ASSOURA"
          data-audio="somVassoura"
          ondragover="permitirSoltarLetra(event)"
          ondragleave="sairDropLetra(event)"
          ondrop="soltarLetra(event)"
        >
          <span class="texto-palavra">_ASSOURA</span>

          <img src="../img/vassoura.png" alt="Vassoura" class="imagem-palavra" />
        </div>
      </div>
    </div>

    <audio id="audioIniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>

    <audio id="somLetraT" src="../audios/Letra T.m4a"></audio>

    <audio id="somLetraU" src="../audios/Letra U.m4a"></audio>

    <audio id="somLetraV" src="../audios/Letra V.m4a"></audio>

    <audio id="somTatu" src="../audios/Tatu.mp3"></audio>

    <audio id="somTapete" src="../audios/Tapete.mp3"></audio>

    <audio id="somUva" src="../audios/Uva.mp3"></audio>

    <audio id="somUrso" src="../audios/Urso.mp3"></audio>
    
    <audio id="somVaca" src="../audios/Vaca.mp3"></audio>

    <audio id="somVassoura" src="../audios/Vassoura.mp3"></audio>

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
                        fetch('atv9n1.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'concluir_fase=9'
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