<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Atualiza o progresso no banco para liberar a próxima fase ao concluir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_fase'])) {
    header('Content-Type: application/json');
    $aluno_id = $_SESSION['aluno_id'] ?? 1;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO progresso (aluno_id, estrutura_id, nivel_atual, licoes_concluidas)
            VALUES (:aluno_id, 2, 2, 1)
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
    <title>LUMI - Atividade 2 (Nível 2)</title>
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

    <!-- ETAPA 1: Apresentação das Sílabas da letra C -->
    <div id="etapa1" class="etapa-container">
        <div class="grid-cards-etapa1">
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>CA</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somCA')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>CE</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somCE')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>CI</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somCI')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>CO</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somCO')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-f3"><span>CU</span></div>
                <button class="btn-som-f3" onclick="tocarSom('somCU')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
        </div>

        <button class="btn-avancar-f3" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

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

            <div class="box-letra-c-e2">
                <button class="btn-som-f3" onclick="tocarSom('somC')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
                <div class="circulo-letra-c">C</div>
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
                <div class="quadrado-drop" data-esperado="CA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="CE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="CI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="CO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="CU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
            </div>
        </div>

        <div class="container-opcoes-arrastaveis">
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CO">CO</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CA">CA</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CU">CU</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CE">CE</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CI">CI</div>
        </div>
    </div>

    <!-- ÁUDIOS DAS SÍLABAS E PALAVRAS -->
    <!-- ETAPA 3 -->
    <div id="etapa3" class="etapa-container esconder">
        <div class="container-topo-e3">
            <button class="btn-som-f3" onclick="tocarSom('somInstrucaoE3')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>

        <div class="fila-fotos-e3">
            <div class="card-foto-item">
                <img src="../img/casa.png" alt="Casa">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="CA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
            <div class="card-foto-item">
                <img src="../img/cenoura.png" alt="Cenoura">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="CE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
            <div class="card-foto-item">
                <img src="../img/circo.png" alt="Circo">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="CI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
            <div class="card-foto-item">
                <img src="../img/coelho.png" alt="Coelho">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="CO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
            <div class="card-foto-item">
                <img src="../img/cubo.jpeg" alt="Cubo">
                <div class="quadrado-drop drop-silaba-e3" data-esperado="CU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilabaEtapa3(event)"></div>
            </div>
        </div>

        <div class="container-blocos-baixo">
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CA">CA</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CE">CE</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CI">CI</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CO">CO</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="CU">CU</div>
        </div>
    </div>
    <!-- ETAPA 4 (ARRASTAR IMAGENS PARA AS SÍLABAS) -->
    <div id="etapa4" class="etapa-container esconder">
        <div class="painel-etapa4-container">
            <div class="coluna-silabas-e4">
        <div class="linha-bi-e4">
            <button class="btn-som-f3" onclick="tocarSom('somCA')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <div class="card-silaba-e4" data-esperado="CA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">CA</div>
        </div>
        <div class="linha-bi-e4">
            <button class="btn-som-f3" onclick="tocarSom('somCE')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <div class="card-silaba-e4" data-esperado="CE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">CE</div>
        </div>
        <div class="linha-bi-e4">
            <button class="btn-som-f3" onclick="tocarSom('somCI')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <div class="card-silaba-e4" data-esperado="CI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">CI</div>
        </div>
        <div class="linha-bi-e4">
            <button class="btn-som-f3" onclick="tocarSom('somCO')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <div class="card-silaba-e4" data-esperado="CO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">CO</div>
        </div>
        <div class="linha-bi-e4">
            <button class="btn-som-f3" onclick="tocarSom('somCU')">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <div class="card-silaba-e4" data-esperado="CU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarImagemEtapa4(event)">CU</div>
        </div>
            </div>

            <div class="grid-15-imagens-e4">
                <!-- Certas -->
                <div class="card-img-e4" id="e4-img-1" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CA">
                    <img src="../img/casa.png" alt="Casa" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-2" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CA">
                    <img src="../img/cachorro.png" alt="Cachorro" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-3" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CE">
                    <img src="../img/cenoura.png" alt="Cenoura" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-4" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CE">
                    <img src="../img/celular.jpg" alt="Celular" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-5" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CI">
                    <img src="../img/circo.png" alt="Circo" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-6" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CI">
                    <img src="" alt="" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-7" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CO">
                    <img src="../img/coelho.png" alt="Coelho" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-8" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CO">
                    <img src="" alt="../img/coruja.png" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-9" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CU">
                    <img src="../img/cubo.png" alt="Cubo" onerror="this.src='../img/logo.png'">
                </div>
                <div class="card-img-e4" id="e4-img-10" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="CU">
                    <img src="" alt="" onerror="this.style.display='none'">
                </div>
                <!-- Pegadinhas / Erradas -->
                <div class="card-img-e4" id="e4-img-11" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/abelha.png" alt="" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-12" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/dado.png" alt="" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-13" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/estrela.png" alt="" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-14" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/baleia.png" alt="" onerror="this.style.display='none'">
                </div>
                <div class="card-img-e4" id="e4-img-15" draggable="true" ondragstart="arrastarImagemE4(event)" data-silaba-correta="ERRADA">
                    <img src="../img/Jacare.png" alt="" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </div>
    <audio id="somCA" src="../audios/Silabas/CA.mp3"></audio>
    <audio id="somCE" src="../audios/Silabas/CE.mp3"></audio>
    <audio id="somCI" src="../audios/Silabas/CI.mp3"></audio>
    <audio id="somCO" src="../audios/Silabas/CO.mp3"></audio>
    <audio id="somCU" src="../audios/Silabas/CU.mp3"></audio>

    <audio id="somInstrucaoE3" src="../audios/instrucao_etapa3.m4a"></audio>
    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>
    <audio id="somErro" src="../audios/erro.mp3"></audio>
    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>
    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
        <source src="../audios/fundolumi.mp3" type="audio/mpeg">
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

        function mostrarErroArraste(el) {
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
            e.dataTransfer.setData("text/plain", e.target.dataset.silaba);
        }

        function permitirDrop(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            if (!dropzone.classList.contains('concluido')) {
                dropzone.classList.add('hover-drop');
            }
        }

        function sairDrop(e) {
            e.currentTarget.classList.remove('hover-drop');
        }

        let acertosEtapa2 = 0;
        const TOTAL_ETAPA2 = 5;

        function soltarSilaba(e) {
            e.preventDefault();
            const dropzone = e.currentTarget;
            dropzone.classList.remove('hover-drop');

            if (dropzone.classList.contains('concluido')) return;

            const silabaArrastada = e.dataTransfer.getData("text/plain");
            const silabaEsperada = dropzone.dataset.esperado;

            if (silabaArrastada === silabaEsperada) {
                tocarSom('somAcerto');
                
                dropzone.textContent = silabaEsperada;
                dropzone.classList.add('concluido');
                
                const arrastavelUsado = document.querySelector(`.silaba-opcao[data-silaba="${silabaArrastada}"]`);
                if (arrastavelUsado) {
                    arrastavelUsado.style.visibility = 'hidden';
                }

                acertosEtapa2++;

                if (acertosEtapa2 === TOTAL_ETAPA2) {
                    setTimeout(() => {
                        mudarEtapa('etapa2', 'etapa3');
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
                mostrarErroArraste(dropzone);
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
                mostrarErroArraste(cardImg);
            }
        }

        function concluirAtividade() {
            document.getElementById('tela-parabens').classList.remove('esconder');
            gerarConfetes();
            tocarSom('somParabens');

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'concluir_fase=1'
            }).catch(err => console.error("Erro ao registrar progresso:", err));

            setTimeout(() => {
                window.location.href = "nivel2.php";
            }, 4500);
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