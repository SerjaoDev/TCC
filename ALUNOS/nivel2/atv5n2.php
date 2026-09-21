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
            VALUES (:aluno_id, 2, 5, 1)
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
    <title>LUMI - Atividade G (Nível 2)</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    
    <script src="https://cdn.jsdelivr.net/npm/drag-drop-touch-polyfill@1.0.2/DragDropTouch.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f7f3cb;
            font-family: 'Fredoka', 'Comic Sans MS', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        #tela-transicao {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: url('../img/iniciando1.png') no-repeat center center / cover;
            z-index: 9999;
            transition: opacity 1s ease-out, visibility 1s ease-out;
        }

        .nuvem-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 50%;
            pointer-events: none;
            overflow: hidden;
        }

        .nuvem {
            position: absolute;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 50px;
            opacity: 0.8;
            animation: moverNuvem linear infinite;
        }

        .nuvem::before, .nuvem::after {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 50%;
        }

        .nuvem1 { width: 140px; height: 45px; top: 15%; left: -150px; animation-duration: 8s; }
        .nuvem1::before { width: 50px; height: 50px; top: -20px; left: 25px; }
        .nuvem1::after { width: 40px; height: 40px; top: -15px; left: 60px; }

        .nuvem2 { width: 180px; height: 55px; top: 35%; left: -200px; animation-duration: 11s; animation-delay: 1s; }
        .nuvem2::before { width: 65px; height: 65px; top: -25px; left: 30px; }
        .nuvem2::after { width: 50px; height: 50px; top: -20px; left: 80px; }

        .nuvem3 { width: 120px; height: 40px; top: 8%; left: -130px; animation-duration: 9.5s; animation-delay: 2.5s; }
        .nuvem3::before { width: 45px; height: 45px; top: -15px; left: 20px; }
        .nuvem3::after { width: 35px; height: 35px; top: -10px; left: 50px; }

        @keyframes moverNuvem {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(100vw + 250px)); }
        }

        .esconder {
            display: none !important;
        }

        .transicao-oculta {
            opacity: 0 !important;
            visibility: hidden !important;
        }

        #tela-parabens {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #fbf5c8;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
        }

        .gramado-parabens {
            position: absolute;
            bottom: -50px;
            left: -5%;
            width: 110%;
            height: 180px;
            background-color: #4cd964;
            border-radius: 50% 50% 0 0;
            z-index: 2;
        }

        .conteudo-parabens-centro {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 5;
            width: 100%;
        }

        .texto-parabens-colorido {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 20px;
            animation: pulsarLetras 1.5s infinite alternate ease-in-out;
        }

        .texto-parabens-colorido span {
            font-size: 68px;
            font-weight: 900;
            text-shadow: 2px 2px 0px #fff, -2px -2px 0px #fff, 2px -2px 0px #fff, -2px 2px 0px #fff, 0px 4px 6px rgba(0,0,0,0.15);
        }

        .char-p1 { color: #ff5252; }
        .char-a1 { color: #ff7043; }
        .char-r  { color: #7e57c2; }
        .char-a2 { color: #26a69a; }
        .char-b  { color: #9ccc65; }
        .char-e  { color: #fbc02d; }
        .char-s1 { color: #ec407a; }
        .char-n  { color: #42a5f5; }
        .char-s2 { color: #00bcd4; }

        @keyframes pulsarLetras {
            0% { transform: scale(0.95); }
            100% { transform: scale(1.05); }
        }

        .mascote-parabens {
            width: 210px;
            height: auto;
            animation: flutuarMascote 2s infinite alternate ease-in-out;
        }

        @keyframes flutuarMascote {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }

        .nuvem-parabens-esq {
            position: absolute;
            top: 22%;
            left: 8%;
            width: 240px;
            opacity: 0.9;
            z-index: 3;
        }

        .nuvem-parabens-dir {
            position: absolute;
            top: 12%;
            right: 8%;
            width: 260px;
            opacity: 0.9;
            z-index: 3;
        }

        .container-confetes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 4;
            overflow: hidden;
        }

        .confete {
            position: absolute;
            top: -20px;
            animation: cairConfete linear infinite;
        }

        .confete.retangulo { width: 12px; height: 20px; border-radius: 2px; }
        .confete.circulo { width: 12px; height: 12px; border-radius: 50%; }
        .confete.serpentina { width: 6px; height: 28px; border-radius: 10px; }

        @keyframes cairConfete {
            0% { transform: translateY(0) rotate(0deg) translateX(0); opacity: 1; }
            100% { transform: translateY(105vh) rotate(720deg) translateX(50px); opacity: 0.8; }
        }

        @keyframes balancarErro {
            0%, 100% { transform: translateX(0); }
            15% { transform: translateX(-10px); }
            30% { transform: translateX(9px); }
            45% { transform: translateX(-7px); }
            60% { transform: translateX(6px); }
            75% { transform: translateX(-4px); }
            90% { transform: translateX(3px); }
        }

        .erro-arraste {
            animation: balancarErro 0.6s ease-in-out;
            background-color: rgba(255, 82, 82, 0.35) !important;
            border-color: #ff5252 !important;
            box-shadow: 0 0 0 3px rgba(255, 82, 82, 0.5) !important;
        }

        .btn-voltar {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            background: #ffffff;
            border: 2px solid #1a1a1a;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #1a1a1a;
            font-size: 20px;
            font-weight: bold;
            z-index: 20;
            box-shadow: 0 4px 0 #1a1a1a;
        }

        .circulo { position: absolute; border-radius: 50%; z-index: 1; }
        .circulo-azul { width: 260px; height: 260px; background-color: #a8c3d1; top: -50px; left: -50px; }
        .circulo-rosa { width: 300px; height: 300px; background-color: #f7d3ca; left: 22%; bottom: 18%; }
        .circulo-verde { width: 280px; height: 280px; background-color: #b1e0a8; top: 12%; right: 18%; }
        .circulo-amarelo { width: 320px; height: 320px; background-color: #fce892; bottom: -80px; right: -50px; }

        .etapa-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 950px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- ETAPA 1: SÍLABAS DO G --- */
        .grid-cards-etapa1 {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
            margin-bottom: 25px;
        }

        .card-bloco-etapa1 {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .card-amarelo-g3 {
            width: 140px;
            height: 140px;
            background-color: #ffeb60;
            border: 3.5px solid #1a1a1a;
            border-radius: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 6px 0px #1a1a1a;
        }

        .card-amarelo-g3 span {
            font-size: 48px;
            font-weight: 900;
            color: #1a1a1a;
        }

        .btn-som-g3 {
            width: 52px;
            height: 52px;
            background-color: #ffffff;
            border: 2.5px solid #1a1a1a;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 3px 0px #1a1a1a;
            transition: transform 0.1s;
        }

        .btn-som-g3:active {
            transform: translateY(2px);
            box-shadow: 0 1px 0px #1a1a1a;
        }

        .btn-avancar-g3 {
            width: 100%;
            max-width: 780px;
            height: 50px;
            background-color: transparent;
            border: 2.5px solid #1a1a1a;
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 24px;
            color: #1a1a1a;
            transition: background-color 0.2s;
        }

        .btn-avancar-g3:hover {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        /* --- ETAPA 2: LIGAÇÃO DA LETRA G --- */
        .painel-ligacao-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 900px;
            height: 480px;
        }

        .svg-setas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .circulo-letra-b {
            position: absolute;
            left: 80px;
            width: 80px;
            height: 80px;
            border: 3px solid #1a1a1a;
            border-radius: 50%;
            background-color: #a8e0d2;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 42px;
            font-weight: 900;
            color: #1a1a1a;
            z-index: 2;
            box-shadow: 0 4px 0px #1a1a1a;
        }

        .coluna-vogais-g2 {
            position: absolute;
            left: 280px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 2;
        }

        .circulo-vogal {
            width: 60px;
            height: 60px;
            border: 3px solid #1a1a1a;
            border-radius: 50%;
            background-color: #a8e0d2;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 32px;
            font-weight: 900;
            color: #1a1a1a;
            box-shadow: 0 3px 0px #1a1a1a;
        }

        .coluna-igual {
            position: absolute;
            left: 360px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 2;
        }

        .sinal-igual {
            height: 60px;
            display: flex;
            align-items: center;
            font-size: 32px;
            font-weight: 900;
            color: #1a1a1a;
        }

        .coluna-drops {
            position: absolute;
            left: 410px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 2;
        }

        .quadrado-drop {
            width: 110px;
            height: 60px;
            border: 3px solid #1a1a1a;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 32px;
            font-weight: 900;
            color: #1a1a1a;
            box-shadow: 0 3px 0px #1a1a1a;
            transition: all 0.2s;
        }

        .quadrado-drop.hover-drop {
            background-color: #fff4a3;
        }

        .quadrado-drop.concluido {
            background-color: #b1e0a8;
        }

        .coluna-figuras-alinhadas {
            position: absolute;
            left: 550px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 2;
        }

        .card-figura-alinhada {
            width: 260px;
            height: 60px;
            border: 3px solid #1a1a1a;
            border-radius: 15px;
            background-color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            box-shadow: 0 3px 0px #1a1a1a;
        }

        .img-figura-g2 {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .texto-figura-g2 {
            font-size: 26px;
            font-weight: 900;
            color: #1a1a1a;
        }

        .container-opcoes-arrastaveis {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            z-index: 10;
        }

        .silaba-opcao {
            width: 80px;
            height: 55px;
            background-color: #ffeb60;
            border: 3px solid #1a1a1a;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            font-weight: 900;
            color: #1a1a1a;
            cursor: grab;
            box-shadow: 0 4px 0px #1a1a1a;
            user-select: none;
        }

        .silaba-opcao:active {
            cursor: grabbing;
        }

    </style>
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

    <!-- ETAPA 1: Apresentação das Sílabas da letra G -->
    <div id="etapa1" class="etapa-container">
        <div class="grid-cards-etapa1">
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-g3"><span>GA</span></div>
                <button class="btn-som-g3" onclick="tocarSom('somGA')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-g3"><span>GE</span></div>
                <button class="btn-som-g3" onclick="tocarSom('somGE')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-g3"><span>GI</span></div>
                <button class="btn-som-g3" onclick="tocarSom('somGI')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-g3"><span>GO</span></div>
                <button class="btn-som-g3" onclick="tocarSom('somGO')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
            <div class="card-bloco-etapa1">
                <div class="card-amarelo-g3"><span>GU</span></div>
                <button class="btn-som-g3" onclick="tocarSom('somGU')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
            </div>
        </div>

        <button class="btn-avancar-g3" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

    <!-- ETAPA 2: Ligação G -> Vogais + Imagens Alinhadas -->
    <div id="etapa2" class="etapa-container esconder">
        <div class="painel-ligacao-container">
            <svg class="svg-setas">
                <defs>
                    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#1a1a1a"/>
                    </marker>
                </defs>
                <line x1="160" y1="240" x2="275" y2="50" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="160" y1="240" x2="275" y2="125" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="160" y1="240" x2="275" y2="240" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="160" y1="240" x2="275" y2="355" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
                <line x1="160" y1="240" x2="275" y2="430" stroke="#1a1a1a" stroke-width="2.5" marker-end="url(#arrow)" />
            </svg>

            <!-- Círculo com a letra G -->
            <div class="circulo-letra-b">G</div>

            <!-- Coluna de Círculos com Vogais -->
            <div class="coluna-vogais-g2">
                <div class="circulo-vogal">A</div>
                <div class="circulo-vogal">E</div>
                <div class="circulo-vogal">I</div>
                <div class="circulo-vogal">O</div>
                <div class="circulo-vogal">U</div>
            </div>

            <!-- Coluna do Sinal de Igual -->
            <div class="coluna-igual">
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
                <div class="sinal-igual">=</div>
            </div>

            <!-- Coluna de Drop dos Quadrados das Sílabas -->
            <div class="coluna-drops">
                <div class="quadrado-drop" data-esperado="GA" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="GE" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="GI" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="GO" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
                <div class="quadrado-drop" data-esperado="GU" ondragover="permitirDrop(event)" ondragleave="sairDrop(event)" ondrop="soltarSilaba(event)"></div>
            </div>

            <!-- Coluna de Figuras Correspondentes Alinhadas (Letra G) -->
            <div class="coluna-figuras-alinhadas">
                <div class="card-figura-alinhada">
                    <span class="texto-figura-g2">GATO</span>
                    <img src="../img/gato.png" class="img-figura-g2" alt="Gato">
                </div>
                <div class="card-figura-alinhada">
                    <span class="texto-figura-g2">GELO</span>
                    <img src="../img/gelo.png" class="img-figura-g2" alt="Gelo">
                </div>
                <div class="card-figura-alinhada">
                    <span class="texto-figura-g2">GIRAFA</span>
                    <img src="../img/girafa.png" class="img-figura-g2" alt="Girafa">
                </div>
                <div class="card-figura-alinhada">
                    <span class="texto-figura-g2">GOTA</span>
                    <img src="../img/gota.png" class="img-figura-g2" alt="Gota">
                </div>
                <div class="card-figura-alinhada">
                    <span class="texto-figura-g2">GUDI</span>
                    <img src="../img/gudi.png" class="img-figura-g2" alt="Gudi">
                </div>
            </div>
        </div>

        <!-- Sílabas da letra G para arrastar -->
        <div class="container-opcoes-arrastaveis">
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="GO">GO</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="GA">GA</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="GU">GU</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="GE">GE</div>
            <div class="silaba-opcao" draggable="true" ondragstart="arrastarSilaba(event)" data-silaba="GI">GI</div>
        </div>
    </div>

    <!-- ÁUDIOS DAS SÍLABAS E PALAVRAS -->
    <audio id="somGA" src="../audios/Silaba GA.m4a"></audio>
    <audio id="somGE" src="../audios/Silaba GE.m4a"></audio>
    <audio id="somGI" src="../audios/Silaba GI.m4a"></audio>
    <audio id="somGO" src="../audios/Silaba GO.m4a"></audio>
    <audio id="somGU" src="../audios/Silaba GU.m4a"></audio>

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
                el.play();
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
                        fetch(window.location.href, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'concluir_fase=1'
                        }).finally(() => {
                            document.getElementById('tela-parabens').classList.remove('esconder');
                            gerarConfetes();
                            tocarSom('somParabens');
                            
                            setTimeout(() => {
                                window.location.href = "nivel2.php";
                            }, 4500);
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