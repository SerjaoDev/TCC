<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_fase'])) {
    header('Content-Type: application/json');

    $aluno_id = $_SESSION['aluno_id'] ?? 1;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO progresso (aluno_id, nivel_atual, licoes_concluidas)
            VALUES (:aluno_id, 2, 1)
            ON DUPLICATE KEY UPDATE 
                nivel_atual = GREATEST(nivel_atual, 2),
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
    <title>LUMI - Atividade 2 (Nível 1)</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
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

        .esconder { display: none !important; }

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

        .etapa-simples { max-width: 450px; }

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

        .btn-palavra:active { transform: scale(0.98); }
        .btn-palavra img { width: 42px; height: 42px; object-fit: contain; }

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

        .btn-avancar:disabled,
        .btn-avancar.bloqueado {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
            border-color: #888;
            color: #888;
        }

        .controles-etapa {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            max-width: 450px;
            margin-top: 15px;
        }

        .btn-som-pequeno {
            background: transparent;
            border: 2px solid #1a1a1a;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 3px 0 #1a1a1a;
            flex-shrink: 0;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .btn-som-pequeno:hover { transform: scale(1.1); }

        #etapa7 {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 1000px;
        }

        .conteudo-etapa7-jogo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            width: 100%;
        }

        .painel-dropzones {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .card-drop {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 25px;
            width: 130px;
            height: 130px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 6px 0px #1a1a1a;
            font-size: 55px;
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
            width: 75px;
            height: 75px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 0px #1a1a1a;
            cursor: grab;
            touch-action: none;
            transition: transform 0.1s, opacity 0.3s;
        }

        .item-figura:active { cursor: grabbing; transform: scale(1.08); }
        .item-figura img { width: 52px; height: 52px; object-fit: contain; pointer-events: none; }
        .item-figura.concluido { opacity: 0.2; pointer-events: none; box-shadow: none; }

        #etapa8 {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
            max-width: 950px;
        }

        .container-completar-grid {
            display: flex;
            gap: 25px;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .opcoes-letras-lateral {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .card-letra-opcao {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 20px;
            width: 85px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 42px;
            font-weight: 900;
            box-shadow: 0 5px 0px #1a1a1a;
            cursor: grab;
            touch-action: none;
            user-select: none;
        }

        .card-letra-opcao:active {
            cursor: grabbing;
            transform: scale(1.05);
        }

        .grid-cards-palavras {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 18px;
        }

        .card-completar {
            background-color: #fde05f;
            border: 3px solid #1a1a1a;
            border-radius: 20px;
            width: 270px;
            height: 52px;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 0px #1a1a1a;
            transition: transform 0.2s, background-color 0.2s;
        }

        .card-completar.hoover {
            transform: scale(1.03);
            background-color: #fff4a3;
        }

        .card-completar.concluido {
            background-color: #b1e0a8;
            border-color: #1a1a1a;
        }

        .palavra-lacuna {
            font-size: 24px;
            font-weight: 900;
            color: #000;
            display: flex;
            align-items: center;
            letter-spacing: 1px;
        }

        .drop-lacuna {
            display: inline-block !important;
            min-width: 28px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border: none;
            background-color: transparent;
            margin-right: 2px;
            font-size: 26px;
            font-weight: 900;
            color: #000;
            box-sizing: border-box;
        }

        .card-completar img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .arrastando-clone {
            position: fixed;
            pointer-events: none !important;
            z-index: 99999 !important;
            opacity: 0.9;
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0,0,0,0.3);
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
        <img src="../img/coelho2.png" alt="Coelho" class="icone-card" onerror="this.style.display='none'">
        <div class="card-letra">
            <span>Cc</span>
        </div>
        <button class="btn-som" onclick="tocarSom('somLetraC')" title="Ouvir som da letra C">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
        </button>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa1', 'etapa2')">➔</button>
</div>

<div id="etapa2" class="etapa-container esconder">
    <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
            <img src="../img/coelho2.png" alt="Coelho" class="icone-card" onerror="this.style.display='none'">
            <div class="card-letra">
                <span>Cc</span>
            </div>
            <button class="btn-som" onclick="tocarSom('somLetraC')" title="Ouvir som da letra C">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>

        <div class="lista-palavras">
            <button class="btn-palavra" onclick="tocarSom('somCoelho')">
                <span>COELHO</span>
                <img src="../img/coelho.png" alt="Coelho">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somCasa')">
                <span>CASA</span>
                <img src="../img/casa.png" alt="Casa">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somCoruja')">
                <span>CORUJA</span>
                <img src="../img/coruja.png" alt="Coruja">
            </button>
        </div>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa2', 'etapa3')">➔</button>
</div>

<div id="etapa3" class="etapa-container etapa-simples esconder">
    <div class="card-letra-wrapper">
        <img src="../img/dado2.png" alt="Dado" class="icone-card" onerror="this.style.display='none'">
        <div class="card-letra">
            <span>Dd</span>
        </div>
        <button class="btn-som" onclick="tocarSom('somLetraD')" title="Ouvir som da letra D">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
        </button>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa3', 'etapa4')">➔</button>
</div>

<div id="etapa4" class="etapa-container esconder">
    <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
            <img src="../img/dado2.png" alt="Dado" class="icone-card" onerror="this.style.display='none'">
            <div class="card-letra">
                <span>Dd</span>
            </div>
            <button class="btn-som" onclick="tocarSom('somLetraD')" title="Ouvir som da letra D">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>

        <div class="lista-palavras">
            <button class="btn-palavra" onclick="tocarSom('somDado')">
                <span>DADO</span>
                <img src="../img/dado.png" alt="Dado">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somDente')">
                <span>DENTE</span>
                <img src="../img/dente.png" alt="Dente">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somDoce')">
                <span>DOCE</span>
                <img src="../img/doce.png" alt="Doce">
            </button>
        </div>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa4', 'etapa5')">➔</button>
</div>

<div id="etapa5" class="etapa-container etapa-simples esconder">
    <div class="card-letra-wrapper">
        <img src="../img/estrela2.png" alt="Estrela" class="icone-card" onerror="this.style.display='none'">
        <div class="card-letra">
            <span>Ee</span>
        </div>
        <button class="btn-som" onclick="tocarSom('somLetraE')" title="Ouvir som da letra E">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
        </button>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa5', 'etapa6')">➔</button>
</div>

<div id="etapa6" class="etapa-container esconder">
    <div class="conteudo-etapa">
        <div class="card-letra-wrapper">
            <img src="../img/estrela2.png" alt="Estrela" class="icone-card" onerror="this.style.display='none'">
            <div class="card-letra">
                <span>Ee</span>
            </div>
            <button class="btn-som" onclick="tocarSom('somLetraE')" title="Ouvir som da letra E">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>

        <div class="lista-palavras">
            <button class="btn-palavra" onclick="tocarSom('somElefante')">
                <span>ELEFANTE</span>
                <img src="../img/elefante.png" alt="Elefante">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somEstrela')">
                <span>ESTRELA</span>
                <img src="../img/estrela.png" alt="Estrela">
            </button>
            <button class="btn-palavra" onclick="tocarSom('somEscova')">
                <span>ESCOVA</span>
                <img src="../img/escova.png" alt="Escova">
            </button>
        </div>
    </div>
    <button class="btn-avancar" onclick="mudarEtapa('etapa6', 'etapa7')">➔</button>
</div>

<div id="etapa7" class="esconder">
    <div class="conteudo-etapa7-jogo">
        <div class="painel-dropzones">
            <div class="card-drop" data-letra="c">
                <span>Cc</span>
            </div>
            <div class="card-drop" data-letra="d">
                <span>Dd</span>
            </div>
            <div class="card-drop" data-letra="e">
                <span>Ee</span>
            </div>
        </div>

        <div class="grid-figuras-12">
            <div class="item-figura" id="fig-coelho" data-letra="c"><img src="../img/coelho.png" alt="Coelho"></div>

            <div class="item-figura" id="fig-dado" data-letra="d"><img src="../img/dado.png" alt="Dado"></div>

            <div class="item-figura" id="fig-casa" data-letra="c"><img src="../img/casa.png" alt="Casa"></div>

            <div class="item-figura" id="fig-coruja" data-letra="c"><img src="../img/coruja.png" alt="Coruja"></div>

            <div class="item-figura" id="fig-estrela" data-letra="e"><img src="../img/estrela.png" alt="Estrela"></div>

            <div class="item-figura" id="fig-dente" data-letra="d"><img src="../img/dente.png" alt="Dente"></div>

            <div class="item-figura" id="fig-doce" data-letra="d"><img src="../img/doce.png" alt="Doce"></div>

            <div class="item-figura" id="fig-melancia" data-letra="nenhuma"><img src="../img/melancia.png" alt="Melancia"></div>

            <div class="item-figura" id="fig-elefante" data-letra="e"><img src="../img/elefante.png" alt="Elefante"></div>
            
            <div class="item-figura" id="fig-escova" data-letra="e"><img src="../img/escova.png" alt="Escova"></div>

            <div class="item-figura" id="fig-abacate" data-letra="nenhuma"><img src="../img/abacate.png" alt="Abacate"></div>

            <div class="item-figura" id="fig-escada" data-letra="e"><img src="../img/escada.png" alt="Escada"></div>
            
        </div>
    </div>

    <div class="controles-etapa">
        <button class="btn-som-pequeno" onclick="tocarSom('audioInstrucao')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
        </button>
        <button class="btn-avancar bloqueado" id="btn-avancar-etapa7" disabled onclick="mudarEtapa('etapa7', 'etapa8')">➔</button>
    </div>
</div>

<div id="etapa8" class="esconder">
    <div class="container-completar-grid">
        <div class="opcoes-letras-lateral">
            <div class="card-letra-opcao" data-letra="d">Dd</div>
            <div class="card-letra-opcao" data-letra="c">Cc</div>
            <div class="card-letra-opcao" data-letra="e">Ee</div>
        </div>

        <div class="grid-cards-palavras">
            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="e">_</span>LEFANTE
                </div>
                <img src="../img/elefante.png" alt="Elefante">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="e">_</span>SCADA
                </div>
                <img src="../img/escada.png" alt="Escada">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="c">_</span>ORUJA
                </div>
                <img src="../img/coruja.png" alt="Coruja">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="d">_</span>ADO
                </div>
                <img src="../img/dado.png" alt="Dado">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="d">_</span>OCE
                </div>
                <img src="../img/doce.png" alt="Doce">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="d">_</span>ENTE
                </div>
                <img src="../img/dente.png" alt="Dente">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="c">_</span>OELHO
                </div>
                <img src="../img/coelho.png" alt="Coelho">
            </div>

            <div class="card-completar">
                <div class="palavra-lacuna">
                    <span class="drop-lacuna" data-correta="e">_</span>SCOVA
                </div>
                <img src="../img/escova.png" alt="Escova">
            </div>
        </div>
    </div>
</div>

<audio id="somLetraC" src="../audios/Letra C.mp3"></audio>
<audio id="somLetraD" src="../audios/Letra D.mp3"></audio>
<audio id="somLetraE" src="../audios/Letra E.mp3"></audio>

<audio id="somCoelho" src="../audios/Coelho.mp3"></audio>
<audio id="somCasa" src="../audios/Casa.mp3"></audio>
<audio id="somCoruja" src="../audios/Coruja.mp3"></audio>

<audio id="somDado" src="../audios/Dado.mp3"></audio>
<audio id="somDente" src="../audios/Dente.mp3"></audio>
<audio id="somDoce" src="../audios/Doce.mp3"></audio>

<audio id="somElefante" src="../audios/Elefante.mp3"></audio>
<audio id="somEstrela" src="../audios/Estrela.mp3"></audio>
<audio id="somEscova" src="../audios/Escova.mp3"></audio>

<audio id="somAcerto" src="../audios/acerto.mp3"></audio>
<audio id="somErro" src="../audios/erro.mp3"></audio>
<audio id="audioInstrucao" src="../audios/instrucao_arrastar.mp3"></audio>
<audio id="audioInstrucaoCompletar" src="../audios/instrucao_completar.mp3"></audio>
<audio id="somParabens" src="../audios/parabens.mp3"></audio>

<audio id="somFundoatv" loop preload="auto">
    <source src="../audios/fundoatv.mp3" type="audio/mpeg">
</audio>

<script>
   window.addEventListener('DOMContentLoaded', () => {

    setTimeout(() => {
        const tela = document.getElementById('tela-transicao');
        if (tela) {
            tela.classList.add('transicao-oculta');
        }
    }, 4000); 
});

    function mudarEtapa(etapaAtual, proximaEtapa) {
        document.getElementById(etapaAtual).classList.add('esconder');
        document.getElementById(proximaEtapa).classList.remove('esconder');

        if (proximaEtapa === 'etapa7') {
            tocarSom('audioInstrucao');
            configurarDragUniversalEtapa7();
        } else if (proximaEtapa === 'etapa8') {
            tocarSom('audioInstrucaoCompletar');
            configurarDragUniversalEtapa8();
        }
    }

    function tocarSom(idAudio) {
        const el = document.getElementById(idAudio);
        if (el) {
            el.currentTime = 0;
            el.play().catch(() => {});
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

    let acertosEtapa7 = 0;
    const TOTAL_ACERTOS_ETAPA7 = 10;

    function processarJogada(figura, dropzone) {
        const letraFigura = figura.getAttribute('data-letra');
        const letraZona = dropzone.getAttribute('data-letra');

        if (letraFigura === letraZona) {
            tocarSom('somAcerto');
            figura.classList.add('concluido');
            acertosEtapa7++;

            if (acertosEtapa7 === TOTAL_ACERTOS_ETAPA7) {
                const btnAvancar7 = document.getElementById('btn-avancar-etapa7');
                if (btnAvancar7) {
                    btnAvancar7.disabled = false;
                    btnAvancar7.classList.remove('bloqueado');
                }
            }
        } else {
            tocarSom('somErro');
            mostrarErroArraste(dropzone);
        }
    }

    let acertosEtapa8 = 0;
    const TOTAL_ACERTOS_ETAPA8 = 8;

    function processarCardPalavra(card, letra) {
        if (card.classList.contains('concluido')) return;

        const lacuna = card.querySelector('.drop-lacuna');
        if (!lacuna) return;

        const letraCorreta = lacuna.getAttribute('data-correta');

        if (letra === letraCorreta) {
            tocarSom('somAcerto');
            lacuna.textContent = letra.toUpperCase();
            card.classList.add('concluido');
            acertosEtapa8++;

            if (acertosEtapa8 === TOTAL_ACERTOS_ETAPA8) {
                setTimeout(() => {
                    finalizarAtividade();
                }, 600);
            }
        } else {
            tocarSom('somErro');
            mostrarErroArraste(card);
        }
    }

    function finalizarAtividade() {
        const urlAtual = window.location.pathname.split('/').pop() || 'atv2n1.php';

        fetch(urlAtual, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'concluir_fase=2'
        })
        .then(r => r.json())
        .then(d => console.log('BD:', d))
        .catch(e => console.error('Erro:', e))
        .finally(() => {
            const telaParabens = document.getElementById('tela-parabens');
            if (telaParabens) {
                telaParabens.classList.remove('esconder');
                gerarConfetes();
                tocarSom('somParabens');
            }
            setTimeout(() => {
                window.location.href = "nivel1.php";
            }, 4500);
        });
    }

    function configurarDragUniversalEtapa7() {
        const figuras = document.querySelectorAll('#etapa7 .item-figura');
        figuras.forEach(figura => {
            figura.addEventListener('pointerdown', (e) => {
                if (figura.classList.contains('concluido')) return;
                iniciarArrasto(e, figura, (alvoSolto) => {
                    const dropzone = alvoSolto ? alvoSolto.closest('.card-drop') : null;
                    if (dropzone) {
                        processarJogada(figura, dropzone);
                    }
                });
            });
        });
    }

    function configurarDragUniversalEtapa8() {
        const opcoes = document.querySelectorAll('#etapa8 .card-letra-opcao');
        opcoes.forEach(opcao => {
            opcao.addEventListener('pointerdown', (e) => {
                iniciarArrasto(e, opcao, (alvoSolto) => {
                    const card = alvoSolto ? alvoSolto.closest('.card-completar') : null;
                    if (card && !card.classList.contains('concluido')) {
                        processarCardPalavra(card, opcao.getAttribute('data-letra'));
                    }
                });
            });
        });
    }

    function iniciarArrasto(event, elementoOriginal, callbackFinalizacao) {
        event.preventDefault();

        const clone = elementoOriginal.cloneNode(true);
        clone.classList.add('arrastando-clone');

        const rect = elementoOriginal.getBoundingClientRect();
        const largura = rect.width;
        const altura = rect.height;

        clone.style.width = `${largura}px`;
        clone.style.height = `${altura}px`;
        clone.style.left = `${event.clientX - largura / 2}px`;
        clone.style.top = `${event.clientY - altura / 2}px`;

        document.body.appendChild(clone);

        function mover(e) {
            clone.style.left = `${e.clientX - largura / 2}px`;
            clone.style.top = `${e.clientY - altura / 2}px`;

            const elementoAbaixo = document.elementFromPoint(e.clientX, e.clientY);
            document.querySelectorAll('.card-drop, .card-completar').forEach(el => el.classList.remove('soltar-hoover', 'hoover'));

            if (elementoAbaixo) {
                const drop = elementoAbaixo.closest('.card-drop');
                const card = elementoAbaixo.closest('.card-completar');
                if (drop) drop.classList.add('soltar-hoover');
                if (card && !card.classList.contains('concluido')) card.classList.add('hoover');
            }
        }

        function soltar(e) {
            window.removeEventListener('pointermove', mover);
            window.removeEventListener('pointerup', soltar);

            document.querySelectorAll('.card-drop, .card-completar').forEach(el => el.classList.remove('soltar-hoover', 'hoover'));

            const elementoAbaixo = document.elementFromPoint(e.clientX, e.clientY);
            clone.remove();

            callbackFinalizacao(elementoAbaixo);
        }

        window.addEventListener('pointermove', mover);
        window.addEventListener('pointerup', soltar);
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

    const audioFundo = document.getElementById('somFundoatv');
    audioFundo.volume = 0.9;
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