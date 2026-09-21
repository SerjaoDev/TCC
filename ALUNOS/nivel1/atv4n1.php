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
            VALUES (:aluno_id, 1, 4, 1) 
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
    <title>LUMI - Atividade 4 (Nível 1)</title>
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

        .card-letra-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-letra {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 35px;
            width: 210px;
            height: 210px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 8px 0px #1a1a1a;
        }

        .card-letra span {
            font-size: 85px;
            font-weight: 900;
            color: #000000;
        }

        .icone-card {
            position: absolute;
            top: -35px;
            right: -20px;
            width: 70px;
        }

        .btn-som {
            background: transparent;
            border: 2px solid #1a1a1a;
            border-radius: 50%;
            width: 55px;
            height: 55px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.2s;
        }

        .btn-som:hover { transform: scale(1.1); }

        .etapa-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 850px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .etapa-simples {
            max-width: 450px;
        }

        .conteudo-etapa {
            display: flex;
            align-items: center;
            justify-content: space-around;
            width: 100%;
            margin-bottom: 25px;
        }

        .lista-palavras {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 320px;
        }

        .btn-palavra {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 20px;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 6px 0px #1a1a1a;
            cursor: pointer;
            font-size: 26px;
            font-weight: bold;
            color: #000000;
            transition: transform 0.1s;
        }

        .btn-palavra:active {
            transform: scale(0.98);
        }

        .btn-palavra img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .btn-avancar {
            width: 100%;
            max-width: 450px;
            height: 45px;
            border: 2px solid #1a1a1a;
            border-radius: 30px;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 26px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-avancar:hover:not(:disabled) {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .btn-avancar:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            border-color: #888888;
            color: #888888;
        }

        #etapa5 {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 1000px;
        }

        .jogo-wrapper-etapa5 {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
        }

        .painel-dropzones {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .card-drop {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 25px;
            width: 140px;
            height: 140px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 6px 0px #1a1a1a;
            font-size: 60px;
            font-weight: 900;
            color: #000;
            transition: transform 0.2s, background-color 0.2s;
        }

        .card-drop.soltar-hoover {
            transform: scale(1.05);
            background-color: #fff29c;
        }

        .grid-figuras-12 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .item-figura {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 18px;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 0px #1a1a1a;
            cursor: grab;
            transition: transform 0.1s, opacity 0.3s;
        }

        .item-figura:active {
            cursor: grabbing;
            transform: scale(1.08);
        }

        .item-figura img {
            width: 55px;
            height: 55px;
            object-fit: contain;
            pointer-events: none;
        }

        .item-figura.concluido {
            opacity: 0.2;
            pointer-events: none;
            box-shadow: none;
        }

        #etapa6 {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            width: 100%;
            max-width: 1000px;
            padding: 20px;
        }

        .painel-letras {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .card-letra-arrastavel {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 30px;
            width: 140px;
            height: 140px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 7px 0px #1a1a1a;
            font-size: 60px;
            font-weight: 900;
            color: #000000;
            cursor: grab;
            user-select: none;
            transition: transform 0.2s;
        }

        .card-letra-arrastavel:active {
            cursor: grabbing;
            transform: scale(1.05);
        }

        .grid-palavras-jogo {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 25px;
        }

        .card-palavra-drop {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 25px;
            width: 320px;
            height: 75px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            box-shadow: 0 6px 0px #1a1a1a;
            transition: transform 0.2s, background-color 0.2s;
        }

        .card-palavra-drop.hover-drop {
            transform: scale(1.03);
            background-color: #fff4a3;
        }

        .card-palavra-drop.concluido {
            background-color: #b1e0a8;
            border-color: #1a1a1a;
        }

        .texto-palavra {
            font-size: 28px;
            font-weight: 900;
            color: #000000;
            letter-spacing: 2px;
        }

        .imagem-palavra {
            width: 50px;
            height: 50px;
            object-fit: contain;
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
            <img src="../img/lumiminho.png" alt="Lumi Abraçando Coração" class="mascote-parabens" onerror="this.src='../img/luminho.png'">
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
                <span>Ii</span>
            </div>
            <button class="btn-som" onclick="tocarSom('somLetraI')" title="Ouvir som da letra I">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>
        <button class="btn-avancar" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

    <div id="etapa2" class="etapa-container esconder">
        <div class="conteudo-etapa">
            <div class="card-letra-wrapper">
                <div class="card-letra">
                    <span>Ii</span>
                </div>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraI')" title="Ouvir som da letra I">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>

            <div class="lista-palavras">
                <button class="btn-palavra" onclick="tocarSom('somIgreja')">
                    <span>IGREJA</span>
                    <img src="../img/Igreja.png" alt="Igreja">
                </button>
                <button class="btn-palavra" onclick="tocarSom('somIguana')">
                    <span>IGUANA</span>
                    <img src="../img/Iguana.png" alt="Iguana">
                </button>
                <button class="btn-palavra" onclick="tocarSom('somIglu')">
                    <span>IGLU</span>
                    <img src="../img/Iglu.png" alt="Iglu">
                </button>
            </div>
        </div>
        <button class="btn-avancar" onclick="mudarEtapa('etapa2', 'etapa3')">➔</button>
    </div>

    <div id="etapa3" class="etapa-container etapa-simples esconder">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Jj</span>
            </div>
            <button class="btn-som" onclick="tocarSom('somLetraJ')" title="Ouvir som da letra J">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>
        <button class="btn-avancar" onclick="mudarEtapa('etapa3', 'etapa4')">➔</button>
    </div>

    <div id="etapa4" class="etapa-container esconder">
        <div class="conteudo-etapa">
            <div class="card-letra-wrapper">
                <div class="card-letra">
                    <span>Jj</span>
                </div>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraJ')" title="Ouvir som da letra J">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>

            <div class="lista-palavras">
                <button class="btn-palavra" onclick="tocarSom('somJacare')">
                    <span>JACARÉ</span>
                    <img src="../img/Jacare.png" alt="Jacaré">
                </button>
                <button class="btn-palavra" onclick="tocarSom('somJanela')">
                    <span>JANELA</span>
                    <img src="../img/Janela.png" alt="Janela">
                </button>
                <button class="btn-palavra" onclick="tocarSom('somJoaninha')">
                    <span>JOANINHA</span>
                    <img src="../img/Joaninha.png" alt="Joaninha">
                </button>
            </div>
        </div>
        <button class="btn-avancar" onclick="mudarEtapa('etapa4', 'etapa5')">➔</button>
    </div>

    <div id="etapa5" class="esconder">
        <div class="jogo-wrapper-etapa5">
        <div class="painel-dropzones">
            <div class="card-drop" data-letra="i" ondragover="permitirSoltar(event)" ondragleave="sairDrop(event)" ondrop="soltar(event)">
                <span>Ii</span>
            </div>

            <div class="card-drop" data-letra="j" ondragover="permitirSoltar(event)" ondragleave="sairDrop(event)" ondrop="soltar(event)">
                <span>Jj</span>
            </div>
        </div>

        <div class="grid-figuras-12">
            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-igreja" data-letra="i">
                <img src="../img/Igreja.png" alt="Igreja">
            </div>

            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-jacare" data-letra="j">
                <img src="../img/Jacare.png" alt="Jacaré">
            </div>

            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-abacate" data-letra="nenhuma">
                <img src="../img/abacate.png" alt="Abacate">
            </div>

            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-iguana" data-letra="i">
                <img src="../img/Iguana.png" alt="Iguana">
            </div>
            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-iglu" data-letra="i">
                <img src="../img/Iglu.png" alt="Iglu">
            </div>

            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-melancia" data-letra="nenhuma">
                <img src="../img/melancia.png" alt="Melancia">
            </div>

            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-janela" data-letra="j">
                <img src="../img/Janela.png" alt="Janela">
            </div>
            <div class="item-figura" draggable="true" ondragstart="arrastar(event)" id="fig-joaninha" data-letra="j">
                <img src="../img/Joaninha.png" alt="Joaninha">
            </div>
        </div>
        
    </div>
        <button id="btn-avancar-etapa5" class="btn-avancar" style="max-width:220px; margin-top:20px;" onclick="mudarEtapa('etapa5', 'etapa6')" disabled>➔</button>
    </div>

    <div id="etapa6" class="esconder">
        <div class="painel-letras">
            <div class="card-letra-arrastavel" draggable="true" ondragstart="arrastarLetra(event)" id="letra-I" data-letra="i">
                <span>Ii</span>
            </div>

            <button class="btn-som" onclick="tocarSom('audioInstrucao')" title="Ouvir instrução">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>

            <div class="card-letra-arrastavel" draggable="true" ondragstart="arrastarLetra(event)" id="letra-J" data-letra="j">
                <span>Jj</span>
            </div>
        </div>

        <div class="grid-palavras-jogo">
            <div class="card-palavra-drop" data-correta="i" data-resto="GREJA" data-audio="somIgreja" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_GREJA</span>
                <img src="../img/Igreja.png" alt="Igreja" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="i" data-resto="GUANA" data-audio="somIguana" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_GUANA</span>
                <img src="../img/Iguana.png" alt="Iguana" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="i" data-resto="GLU" data-audio="somIglu" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_GLU</span>
                <img src="../img/Iglu.png" alt="Iglu" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="j" data-resto="ACARÉ" data-audio="somJacare" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_ACARÉ</span>
                <img src="../img/Jacare.png" alt="Jacaré" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="j" data-resto="ANELA" data-audio="somJanela" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_ANELA</span>
                <img src="../img/Janela.png" alt="Janela" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="j" data-resto="OANINHA" data-audio="somJoaninha" ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_OANINHA</span>
                <img src="../img/Joaninha.png" alt="Joaninha" class="imagem-palavra">
            </div>
        </div>
    </div>

    <audio id="audioIniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>
    <audio id="somLetraI" src="../audios/Letra I.m4a"></audio>
    <audio id="somLetraJ" src="../audios/Letra J.m4a"></audio>

    <audio id="somIgreja" src="../audios/Igreja.mp3"></audio>
    <audio id="somIguana" src="../audios/Iguana.mp3"></audio>
    <audio id="somIglu" src="../audios/Iglu.mp3"></audio>

    <audio id="somJacare" src="../audios/Jacaré.mp3"></audio>
    <audio id="somJanela" src="../audios/Janela.mp3"></audio>
    <audio id="somJoaninha" src="../audios/Joaninha.mp3"></audio>

    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>
    <audio id="somErro" src="../audios/erro.mp3"></audio>
    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>
    <audio id="audioInstrucao" src="../audios/instrucao_arrastar.mp3"></audio>
    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
        <source src="../audios/fundoatv.mp3" type="audio/mpeg">
    </audio>

    <script>
        setTimeout(() => {
            document.getElementById('tela-transicao').classList.add('transicao-oculta');
        }, 4000);

        function mudarEtapa(etapaAtual, proximaEtapa) {
            document.getElementById(etapaAtual).classList.add('esconder');
            document.getElementById(proximaEtapa).classList.remove('esconder');
            if (proximaEtapa === 'etapa6') {
                tocarSom('audioInstrucao');
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
                        fetch('atv4n1.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'concluir_fase=4'
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