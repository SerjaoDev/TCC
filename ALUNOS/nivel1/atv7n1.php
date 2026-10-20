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

        .esconder {
            display: none !important;
        }

        #tela-transicao {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            background: url('../img/iniciando1.png') no-repeat center center / cover;
            z-index: 9999;
            transition: opacity 1s ease-out, visibility 1s ease-out;
        }

        .transicao-oculta {
            opacity: 0 !important;
            visibility: hidden !important;
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
            background: rgba(255, 255, 255, .85);
            border-radius: 50px;
            opacity: .8;
            animation: moverNuvem linear infinite;
        }

        .nuvem::before,
        .nuvem::after {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, .85);
            border-radius: 50%;
        }

        .nuvem1 {
            width: 140px;
            height: 45px;
            top: 15%;
            left: -150px;
            animation-duration: 8s;
        }

        .nuvem1::before {
            width: 50px;
            height: 50px;
            top: -20px;
            left: 25px;
        }

        .nuvem1::after {
            width: 40px;
            height: 40px;
            top: -15px;
            left: 60px;
        }

        .nuvem2 {
            width: 180px;
            height: 55px;
            top: 35%;
            left: -200px;
            animation-duration: 11s;
            animation-delay: 1s;
        }

        .nuvem2::before {
            width: 65px;
            height: 65px;
            top: -25px;
            left: 30px;
        }

        .nuvem2::after {
            width: 50px;
            height: 50px;
            top: -20px;
            left: 80px;
        }

        .nuvem3 {
            width: 120px;
            height: 40px;
            top: 8%;
            left: -130px;
            animation-duration: 9.5s;
            animation-delay: 2.5s;
        }

        .nuvem3::before {
            width: 45px;
            height: 45px;
            top: -15px;
            left: 20px;
        }

        .nuvem3::after {
            width: 35px;
            height: 35px;
            top: -10px;
            left: 50px;
        }

        @keyframes moverNuvem {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(calc(100vw + 250px));
            }
        }

        #tela-parabens {
            position: fixed;
            inset: 0;
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
            text-shadow:
                2px 2px 0 #fff,
                -2px -2px 0 #fff,
                2px -2px 0 #fff,
                -2px 2px 0 #fff,
                0 4px 6px rgba(0, 0, 0, .15);
        }

        .char-p1 {
            color: #ff5252;
        }

        .char-a1 {
            color: #ff7043;
        }

        .char-r {
            color: #7e57c2;
        }

        .char-a2 {
            color: #26a69a;
        }

        .char-b {
            color: #9ccc65;
        }

        .char-e {
            color: #fbc02d;
        }

        .char-s1 {
            color: #ec407a;
        }

        .char-s2 {
            color: #42a5f5;
        }

        @keyframes pulsarLetras {
            0% {
                transform: scale(.95);
            }

            100% {
                transform: scale(1.05);
            }
        }

        .mascote-parabens {
            width: 210px;
            animation: flutuarMascote 2s infinite alternate ease-in-out;
        }

        @keyframes flutuarMascote {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-10px);
            }
        }

        .nuvem-parabens-esq,
        .nuvem-parabens-dir {
            position: absolute;
            opacity: .9;
            z-index: 3;
        }

        .nuvem-parabens-esq {
            top: 22%;
            left: 8%;
            width: 240px;
        }

        .nuvem-parabens-dir {
            top: 12%;
            right: 8%;
            width: 260px;
        }

        .container-confetes {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 4;
            overflow: hidden;
        }

        .confete {
            position: absolute;
            top: -20px;
            animation: cairConfete linear infinite;
        }

        .confete.retangulo {
            width: 12px;
            height: 20px;
            border-radius: 2px;
        }

        .confete.circulo {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .confete.serpentina {
            width: 6px;
            height: 28px;
            border-radius: 10px;
        }

        @keyframes cairConfete {
            0% {
                transform: translateY(0) rotate(0deg) translateX(0);
                opacity: 1;
            }

            100% {
                transform: translateY(105vh) rotate(720deg) translateX(50px);
                opacity: .8;
            }
        }

        .btn-voltar {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            background: #fff;
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

        .circulo {
            position: absolute;
            border-radius: 50%;
            z-index: 1;
        }

        .circulo-azul {
            width: 260px;
            height: 260px;
            background-color: #a8c3d1;
            top: -50px;
            left: -50px;
        }

        .circulo-rosa {
            width: 300px;
            height: 300px;
            background-color: #f7d3ca;
            left: 22%;
            bottom: 18%;
        }

        .circulo-verde {
            width: 280px;
            height: 280px;
            background-color: #b1e0a8;
            top: 12%;
            right: 18%;
        }

        .circulo-amarelo {
            width: 320px;
            height: 320px;
            background-color: #fce892;
            bottom: -80px;
            right: -50px;
        }

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
            box-shadow: 0 8px 0 #1a1a1a;
        }

        .card-letra span {
            font-size: 85px;
            font-weight: 900;
            color: #000;
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
            transition: transform .2s;
        }

        .btn-som:hover {
            transform: scale(1.1);
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
            box-shadow: 0 6px 0 #1a1a1a;
            cursor: pointer;
            font-size: 26px;
            font-weight: bold;
            color: #000;
        }

        .btn-palavra:active {
            transform: scale(.98);
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
        }

        .btn-avancar:hover {
            background-color: #1a1a1a;
            color: #fff;
        }

        #etapa7 {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
            width: 100%;
            max-width: 1050px;
            padding: 20px;
        }

        .painel-letras {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .card-letra-arrastavel {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 30px;
            width: 130px;
            height: 130px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 7px 0 #1a1a1a;
            font-size: 55px;
            font-weight: 900;
            color: #000;
            cursor: grab;
            user-select: none;
        }

        .card-letra-arrastavel:active {
            cursor: grabbing;
            transform: scale(1.05);
        }

        .grid-palavras-jogo {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px 20px;
        }

        .card-palavra-drop {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 25px;
            width: 300px;
            min-height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            box-shadow: 0 6px 0 #1a1a1a;
            transition: transform .2s, background-color .2s;
        }

        .card-palavra-drop.hover-drop {
            transform: scale(1.03);
            background-color: #fff4a3;
        }

        .card-palavra-drop.concluido {
            background-color: #b1e0a8;
        }

        .texto-palavra {
            font-size: 25px;
            font-weight: 900;
            color: #000;
            letter-spacing: 1px;
        }

        .imagem-palavra {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        @keyframes balancarErro {

            0%,
            100% {
                transform: translateX(0);
            }

            15% {
                transform: translateX(-10px);
            }

            30% {
                transform: translateX(9px);
            }

            45% {
                transform: translateX(-7px);
            }

            60% {
                transform: translateX(6px);
            }

            75% {
                transform: translateX(-4px);
            }

            90% {
                transform: translateX(3px);
            }
        }

        .erro-arraste {
            animation: balancarErro .6s ease-in-out;
            background-color: rgba(255, 82, 82, .35) !important;
            border-color: #ff5252 !important;
            box-shadow: 0 0 0 3px rgba(255, 82, 82, .5) !important;
        }

        @media (max-width: 900px) {
            .conteudo-etapa {
                gap: 25px;
            }

            .card-palavra-drop {
                width: 250px;
            }

            #etapa7 {
                gap: 25px;
            }
        }

        @media (max-width: 700px) {
            body {
                overflow-y: auto;
                overflow-x: hidden;
                align-items: flex-start;
            }

            .etapa-container {
                margin: 75px 12px 30px;
                width: calc(100% - 24px);
            }

            .conteudo-etapa {
                flex-direction: column;
                gap: 20px;
            }

            .grid-palavras-jogo {
                grid-template-columns: 1fr;
                width: 100%;
            }

            #etapa7 {
                flex-direction: column;
                margin-top: 65px;
                padding: 15px;
            }

            .painel-letras {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }

            .card-letra-arrastavel {
                width: 85px;
                height: 85px;
                font-size: 38px;
            }

            .card-palavra-drop {
                width: 100%;
            }

            .card-letra {
                width: 160px;
                height: 160px;
            }

            .card-letra span {
                font-size: 65px;
            }

            .lista-palavras {
                width: 100%;
                max-width: 320px;
            }

            .texto-parabens-colorido span {
                font-size: 45px;
            }

            .nuvem-parabens-esq,
            .nuvem-parabens-dir {
                width: 120px;
            }

            .mascote-parabens {
                width: 160px;
            }

            .circulo-rosa {
                left: -20%;
            }

            .circulo-verde {
                right: -20%;
            }
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

    <a href="nivel1.php" class="btn-voltar" title="Voltar para a Trilha">←</a>

    <div class="circulo circulo-azul"></div>
    <div class="circulo circulo-rosa"></div>
    <div class="circulo circulo-verde"></div>
    <div class="circulo circulo-amarelo"></div>

    <!-- ETAPA 1: LETRA P -->
    <div id="etapa1" class="etapa-container etapa-simples">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Pp</span>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraP')" title="Ouvir letra P">🔊</button>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
    </div>

    <!-- ETAPA 2: PALAVRAS COM P -->
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

    <!-- ETAPA 3: LETRA Q -->
    <div id="etapa3" class="etapa-container etapa-simples esconder">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Qq</span>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraQ')">🔊</button>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa3', 'etapa4')">➔</button>
    </div>

    <!-- ETAPA 4: PALAVRAS COM Q -->
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

                <button class="btn-palavra" onclick="tocarSom('somQuati')">
                    <span>QUATI</span>
                    <img src="../img/Quati.png" alt="Quati">
                </button>
            </div>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa4', 'etapa5')">➔</button>
    </div>

    <!-- ETAPA 5: LETRA R -->
    <div id="etapa5" class="etapa-container etapa-simples esconder">
        <div class="card-letra-wrapper">
            <div class="card-letra">
                <span>Rr</span>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraR')">🔊</button>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa5', 'etapa6')">➔</button>
    </div>

    <!-- ETAPA 6: PALAVRAS COM R -->
    <div id="etapa6" class="etapa-container esconder">
        <div class="conteudo-etapa">
            <div class="card-letra-wrapper">
                <div class="card-letra">
                    <span>Rr</span>
                </div>
            </div>

            <button class="btn-som" onclick="tocarSom('somLetraR')">🔊</button>

            <div class="lista-palavras">
                <button class="btn-palavra" onclick="tocarSom('somRato')">
                    <span>RATO</span>
                    <img src="../img/Rato.png" alt="Rato">
                </button>

                <button class="btn-palavra" onclick="tocarSom('somRoda')">
                    <span>RODA</span>
                    <img src="../img/Roda.png" alt="Roda">
                </button>

                <button class="btn-palavra" onclick="tocarSom('somRainha')">
                    <span>RAINHA</span>
                    <img src="../img/Rainha.png" alt="Rainha">
                </button>
            </div>
        </div>

        <button class="btn-avancar" onclick="mudarEtapa('etapa6', 'etapa7')">➔</button>
    </div>

    <!-- ETAPA 7: ARRASTAR AS LETRAS -->
    <div id="etapa7" class="esconder">
        <div class="painel-letras">
            <div class="card-letra-arrastavel" draggable="true" id="letra-P" data-letra="p"
                ondragstart="arrastarLetra(event)">Pp</div>

            <div class="card-letra-arrastavel" draggable="true" id="letra-Q" data-letra="q"
                ondragstart="arrastarLetra(event)">Qq</div>

            <div class="card-letra-arrastavel" draggable="true" id="letra-R" data-letra="r"
                ondragstart="arrastarLetra(event)">Rr</div>

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

            <div class="card-palavra-drop" data-correta="r" data-resto="ATO" data-audio="somRato"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_ATO</span>
                <img src="../img/Rato.png" alt="Rato" class="imagem-palavra">
            </div>

            <div class="card-palavra-drop" data-correta="r" data-resto="ODA" data-audio="somRoda"
                ondragover="permitirSoltarLetra(event)" ondragleave="sairDropLetra(event)" ondrop="soltarLetra(event)">
                <span class="texto-palavra">_ODA</span>
                <img src="../img/Roda.png" alt="Roda" class="imagem-palavra">
            </div>
        </div>
    </div>

    <!-- ÁUDIOS -->
    <audio id="audioIniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>

    <audio id="somLetraP" src="../audios/Letra P.m4a"></audio>
    <audio id="somLetraQ" src="../audios/Letra Q.m4a"></audio>
    <audio id="somLetraR" src="../audios/Letra R.m4a"></audio>

    <audio id="somPato" src="../audios/Pato.mp3"></audio>
    <audio id="somPipa" src="../audios/Pipa.mp3"></audio>
    <audio id="somPanela" src="../audios/Panela.mp3"></audio>

    <audio id="somQueijo" src="../audios/Queijo.mp3"></audio>
    <audio id="somQuadro" src="../audios/Quadro.mp3"></audio>
    <audio id="somQuati" src="../audios/Quati.mp3"></audio>

    <audio id="somRato" src="../audios/Rato.mp3"></audio>
    <audio id="somRoda" src="../audios/Roda.mp3"></audio>
    <audio id="somRainha" src="../audios/Rainha.mp3"></audio>

    <audio id="somAcerto" src="../audios/acerto.mp3"></audio>
    <audio id="somErro" src="../audios/erro.mp3"></audio>
    <audio id="audioTentarNovamente" src="../audios/tentar_novamente.mp3"></audio>
    <audio id="audioInstrucao" src="../audios/instrucao_arrastar.mp3"></audio>
    <audio id="somParabens" src="../audios/parabens.mp3"></audio>

    <audio id="somFundo" loop preload="auto">
        <source src="../audios/fundolumii.mp3" type="audio/mpeg">
    </audio>

    <script>
        // Tela de transição
        setTimeout(() => {
            document.getElementById('tela-transicao')
                .classList.add('transicao-oculta');
        }, 4000);

        // Mudar entre as etapas
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

        // Reproduzir áudio
        function tocarSom(idAudio) {
            const el = document.getElementById(idAudio);

            if (el) {
                el.currentTime = 0;

                el.play().catch(() => { });
            }
        }

        // Mostrar erro no arraste
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

        // Arrastar letra
        function arrastarLetra(event) {
            event.dataTransfer.setData(
                'text/plain',
                event.currentTarget.dataset.letra
            );
        }

        // Permitir soltar a letra
        function permitirSoltarLetra(event) {
            event.preventDefault();

            const dropzone = event.currentTarget;

            if (!dropzone.classList.contains('concluido')) {
                dropzone.classList.add('hover-drop');
            }
        }

        // Remover destaque da zona
        function sairDropLetra(event) {
            event.currentTarget.classList.remove('hover-drop');
        }

        // Contagem de acertos
        let acertosEtapa7 = 0;

        const TOTAL_ACERTOS_ETAPA7 = 6;

        let faseConcluida = false;

        // Soltar letra na palavra
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

        // Registrar conclusão no PHP e mostrar parabéns
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

        // Gerar confetes
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

        // Música de fundo
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