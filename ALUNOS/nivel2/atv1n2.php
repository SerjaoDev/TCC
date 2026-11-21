<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_fase'])) {
    header('Content-Type: application/json');
    $aluno_id = $_SESSION['aluno_id'] ?? 1;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO progresso (aluno_id, estrutura_id, nivel_atual, licoes_concluidas)
            VALUES (:aluno_id, 2, 1, 1)
            ON DUPLICATE KEY UPDATE
                licoes_concluidas = licoes_concluidas + 1
        ");
        $stmt->execute([':aluno_id' => $aluno_id]);

        echo json_encode(['status' => 'sucesso', 'aluno_id' => $aluno_id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - Atividade 1 (Nível 2)</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../CSS/nivel2.css">
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

    <a href="nivel2.php" class="btn-voltar" title="Voltar para a Trilha">←</a>

    <div class="circulo circulo-azul"></div>
    <div class="circulo circulo-rosa"></div>
    <div class="circulo circulo-verde"></div>
    <div class="circulo circulo-amarelo"></div>

    <!-- ETAPA 1 -->
    <div id="etapa1" class="etapa-container">
        <div class="grid-cards-etapa1">
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>BA</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somBA')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>BE</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somBE')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>BI</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somBI')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>BO</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somBO')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>BU</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somBU')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
        </div>

        <button class="btn-avancar-f3" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

    <!-- ETAPA 2 -->
    <div id="etapa2" class="etapa-container esconder">
        <div class="painel-ligacao-container">
            <svg class="svg-setas">
                <defs>
                    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#1a1a1a"/>
                    </marker>
                </defs>
                <line x1="260" y1="240" x2="375" y2="50" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="260" y1="240" x2="375" y2="125" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="260" y1="240" x2="375" y2="240" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="260" y1="240" x2="375" y2="355" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="260" y1="240" x2="375" y2="430" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
            </svg>

            <div class="box-letra-b-e2">
                <button class="btn-som-f3" onclick="tocarSom('somB')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
                <div class="circulo-letra-b">B</div>
            </div>

            <div class="coluna-vogais-f2">
                <div class="circulo-vogal">A</div>
                <div class="circulo-vogal">E</div>
                <div class="circulo-vogal">I</div>
                <div class="circulo-vogal">O</div>
                <div class="circulo-vogal">U</div>
            </div>

            <div class="coluna-igual">
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
            </div>

            <div class="coluna-drops">
                <div class="quadrado-drop" data-esperado="BA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="BE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="BI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="BO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="BU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
            </div>
        </div>

        <div class="container-opcoes-arrastaveis">
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BO">BO</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BA">BA</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BU">BU</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BE">BE</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BI">BI</div>
        </div>
    </div>

    <!-- ETAPA 3 -->
    <div id="etapa3" class="etapa-container esconder">
        <div class="container-topo-e3">
            <button class="btn-som-f3" onclick="tocarSom('somInstrucaoE3')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>

        <div class="fila-fotos-e3">
            <div class="card-foto-item">
                <img src="../img/banana.png" alt="Banana">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="BA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>

            <div class="card-foto-item">
                <img src="../img/bebe.png" alt="Bebê">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="BE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>

            <div class="card-foto-item">
                <img src="../img/bigode.png" alt="Bigode">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="BI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>

            <div class="card-foto-item">
                <img src="../img/bola.png" alt="Bola">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="BO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>

            <div class="card-foto-item">
                <img src="../img/bule.png" alt="Bule">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="BU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
        </div>

        <div class="container-blocos-baixo">
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BU">BU</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BA">BA</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BO">BO</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BE">BE</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="BI">BI</div>
        </div>
    </div>

    <!-- ETAPA 4 (ARRASTAR IMAGENS PARA AS SÍLABAS) -->
    <div id="etapa4" class="etapa-container esconder">
        <div class="painel-etapa4-container">
            <!-- Coluna de Sílabas como áreas de soltar (Dropzones) -->
            <div class="coluna-silabas-e4">
                <div class="card-silaba-e4" data-esperado="BA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">BA</div>
                <div class="card-silaba-e4" data-esperado="BE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">BE</div>
                <div class="linha-bi-e4">
                    <button class="btn-som-f3" onclick="tocarSom('somBI')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    </button>
                    <div class="card-silaba-e4" data-esperado="BI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">BI</div>
                </div>
                <div class="card-silaba-e4" data-esperado="BO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">BO</div>
                <div class="card-silaba-e4" data-esperado="BU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">BU</div>
            </div>

            <!-- Grid de 15 Imagens Arrastáveis (10 certas e 5 pegadinhas) -->
            <div class="grid-15-imagens-e4">
                <!-- Certas (10) -->
                <div class="card-img-e4" id="e4-img-1" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BA">
                    <img src="../img/baleia.png" alt="Baleia" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-2" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BA">
                    <img src="../img/barata.png" alt="Barata" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-3" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BE">
                    <img src="../img/bebe.png" alt="Bebê" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-4" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BE">
                    <img src="../img/bexiga.jpg" alt="Bexiga" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-5" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BI">
                    <img src="../img/bigode.png" alt="Bigode" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-6" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BI">
                    <img src="../img/biquini.png" alt="Biquíni" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-7" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BO">
                    <img src="../img/boneca.png" alt="Boneca" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-8" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BO">
                    <img src="../img/bola.png" alt="Bola" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-9" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BU">
                    <img src="../img/bule.png" alt="Bule" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-10" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="BU">
                    <img src="../img/buzina.png" alt="Buzina" onerror="this.src='../img/logo.png'">
                </div>

                <div class="card-img-e4" id="e4-img-11" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/queijo.png" alt="Queijo" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-12" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/sabonete.png" alt="Sabonete" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-13" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/pirulito.png" alt="Pirulito" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-14" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/maca.png" alt="Maçã" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-15" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/sol.png" alt="Sol" onerror="this.src='../img/logo.png'">
                </div>
            </div>
        </div>
    </div>

    <audio id="somB" src="../audios/letra_b.m4a"></audio>
    <audio id="somBA" src="../audios/Silabas/BA.mp3"></audio>
    <audio id="somBE" src="../audios/Silabas/BE.mp3"></audio>
    <audio id="somBI" src="../audios/Silabas/BI.mp3"></audio>
    <audio id="somBO" src="../audios/Silabas/BO.mp3"></audio>
    <audio id="somBU" src="../audios/Silabas/BU.mp3"></audio>
    <audio id="somInstrucaoE3" src="../audios/instrucao_etapa3.m4a"></audio>
    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>
    <audio id="somErro" src="../audios/erro.mp3"></audio>
    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>
    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
        <source src="../audios/fundoatv.mp3" type="audio/mpeg">
    </audio>

    <script>
        setTimeout(() => {
            document.getElementById('tela-transicao').classList.add('transicao-oculta');
        }, 3000);

        function tocarSom(idAudio) {
            const el = document.getElementById(idAudio);
            if (el) {
                el.currentTime = 0;
                el.play().catch(() => {});
            }
        }

        function mudarEtapa(atual, proxima) {
            document.getElementById(atual).classList.add('esconder');
            document.getElementById(proxima).classList.remove('esconder');
        }

        function mostrarErroElemento(el) {
            if (!el) return;
            el.classList.remove('erro-arraste');
            void el.offsetWidth;
            el.classList.add('erro-arraste');
            tocarSom('audioTentarNovamente');
            setTimeout(() => {
                el.classList.remove('erro-arraste');
            }, 1500);
        }

        function arrastarSilaba(e) {
            e.dataTransfer.setData("text/plain", e.target.dataset.silaba || e.target.textContent);
        }

        function permitirDrop(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            dropzone.classList.add('hover-drop');
        }

        function sairDrop(e) {
            e.currentTarget.classList.remove('hover-drop');
        }

        /* ETAPA 2 */
        let acertosEtapa2 = 0;
        const TOTAL_ETAPA2 = 5;

        function soltarSilaba(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            dropzone.classList.remove('hover-drop');

            if (dropzone.classList.contains('concluido')) return;

            const silabaArrastada = (e.dataTransfer.getData("text/plain") || "").trim().toUpperCase();
            const silabaEsperada = (dropzone.dataset.esperado || "").trim().toUpperCase();

            if (silabaArrastada && silabaArrastada === silabaEsperada) {
                tocarSom('somAcerto');
                
                dropzone.textContent = silabaEsperada;
                dropzone.classList.add('concluido');
                
                const arrastaveis = document.querySelectorAll(`#etapa2 .silaba-opcao[data-silaba="${silabaEsperada}"]`);
                for (let arrastavel of arrastaveis) {
                    if (arrastavel.style.visibility !== 'hidden') {
                        arrastavel.style.visibility = 'hidden';
                        break;
                    }
                }

                acertosEtapa2++;

                if (acertosEtapa2 >= TOTAL_ETAPA2) {
                    setTimeout(() => {
                        mudarEtapa('etapa2', 'etapa3');
                    }, 800);
                }
            } else {
                tocarSom('somErro');
                mostrarErroElemento(dropzone);
            }
        }

        /* ETAPA 3 */
        let acertosEtapa3 = 0;
        const TOTAL_ETAPA3 = 5;

        function soltarSilabaEtapa3(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            dropzone.classList.remove('hover-drop');

            if (dropzone.classList.contains('concluido')) return;

            const silabaArrastada = (e.dataTransfer.getData("text/plain") || "").trim().toUpperCase();
            const silabaEsperada = (dropzone.dataset.esperado || "").trim().toUpperCase();

            if (silabaArrastada && silabaArrastada === silabaEsperada) {
                tocarSom('somAcerto');
                
                dropzone.textContent = silabaEsperada;
                dropzone.classList.add('concluido');
                
                const arrastaveis = document.querySelectorAll(`#etapa3 .silaba-opcao[data-silaba="${silabaEsperada}"]`);
                for (let arrastavel of arrastaveis) {
                    if (arrastavel.style.visibility !== 'hidden') {
                        arrastavel.style.visibility = 'hidden';
                        break;
                    }
                }

                acertosEtapa3++;

                if (acertosEtapa3 >= TOTAL_ETAPA3) {
                    setTimeout(() => {
                        mudarEtapa('etapa3', 'etapa4');
                    }, 800);
                }
            } else {
                tocarSom('somErro');
                mostrarErroElemento(dropzone);
            }
        }

        /* ETAPA 4 - ARRASTAR IMAGENS */
        let acertosEtapa4 = 0;
        const TOTAL_ETAPA4 = 10;
        let idImagemArrastada = null;

        function arrastarImagemE4(e) {
            idImagemArrastada = e.currentTarget.id;
            e.dataTransfer.setData("text/plain", e.currentTarget.id);
        }

        function soltarImagemEtapa4(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            dropzone.classList.remove('hover-drop');

            const imgId = e.dataTransfer.getData("text/plain") || idImagemArrastada;
            const cardImg = document.getElementById(imgId);

            if (!cardImg || cardImg.style.visibility === 'hidden') return;

            const silabaEsperada = (dropzone.dataset.esperado || "").trim().toUpperCase();
            const silabaImagem = (cardImg.dataset.silabaCorreta || "").trim().toUpperCase();

            if (silabaImagem === silabaEsperada) {
                tocarSom('somAcerto');
                cardImg.style.visibility = 'hidden';
                cardImg.style.pointerEvents = 'none';

                acertosEtapa4++;

                if (acertosEtapa4 >= TOTAL_ETAPA4) {
                    setTimeout(() => {
                        concluirAtividade();
                    }, 800);
                }
            } else {
                tocarSom('somErro');
                mostrarErroElemento(cardImg);
            }
        }

        function concluirAtividade() {
            document.getElementById('tela-parabens').classList.remove('esconder');
            gerarConfetes();
            tocarSom('somParabens');

            fetch('atv1n2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'concluir_fase=1'
            }).catch(err => console.error("Erro ao registrar progresso:", err));

            setTimeout(() => {
                window.location.href = "nivel2.php";
            }, 4500);
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
        if (audioFundo) {
            audioFundo.volume = 0.2;
            const tempoSalvo = localStorage.getItem('musica_tempo');
            if (tempoSalvo) audioFundo.currentTime = parseFloat(tempoSalvo);

            audioFundo.play().catch(() => {
                document.addEventListener('click', () => audioFundo.play(), { once: true });
            });

            audioFundo.addEventListener('timeupdate', () => {
                localStorage.setItem('musica_tempo', audioFundo.currentTime);
            });
        }
    </script>
</body>
</html>