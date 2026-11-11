<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Chamado via fetch() quando o aluno termina a atividade: soma 1 lição concluída */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_fase'])) {
    header('Content-Type: application/json');
    $aluno_id = $_SESSION['aluno_id'] ?? 1;

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO progresso (aluno_id, estrutura_id, nivel_atual, licoes_concluidas)
             VALUES (:aluno_id, 1, 12, 1)
             ON DUPLICATE KEY UPDATE licoes_concluidas = licoes_concluidas + 1'
        );
        $stmt->execute([':aluno_id' => $aluno_id]);
        echo json_encode(['status' => 'sucesso', 'aluno_id' => $aluno_id]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['status' => 'erro', 'mensagem' => 'Não foi possível salvar o progresso.']);
    }
    exit;
}

/* ---------- Conteúdo da atividade ----------
   Para incluir uma palavra: adicione em $nomes e em uma das listas abaixo.
   chave  = nome da imagem em ../img/ (.png)
   valor  = palavra exibida (e nome do áudio em ../audios/, .mp3)
   A primeira letra de cada palavra é a vogal correta.                    */
$nomes = [
    'abelha'   => 'Abelha',
    'aviao'    => 'Avião',
    'abacaxi'  => 'Abacaxi',
    'abacate'  => 'Abacate',
    'elefante' => 'Elefante',
    'escada'   => 'Escada',
    'iguana'   => 'Iguana',
    'igreja'   => 'Igreja',
    'ovo'      => 'Ovo',
    'olho'     => 'Olho',
    'uva'      => 'Uva',
    'urso'     => 'Urso',
];

$vogais = ['a', 'e', 'i', 'o', 'u'];
$etapa2 = ['abelha', 'aviao', 'abacaxi'];
$etapa4 = ['abacate', 'elefante', 'iguana', 'olho', 'uva'];
$etapa5 = ['abelha', 'aviao', 'elefante', 'escada', 'iguana', 'igreja', 'ovo', 'olho', 'uva', 'urso'];
$etapa6 = ['abelha', 'elefante', 'iguana', 'ovo', 'uva', 'aviao', 'escada', 'igreja', 'olho', 'urso'];

/* Áudios: o id no HTML será "som-<chave>" e é tocado com tocarSom('<chave>') */
$sons = [
    'acerto'          => 'acerto.mp3',
    'erro'            => 'erro.mp3',
    'tentarNovamente' => 'tentar_novamente.mp3',
    'instrucao'       => 'instrucao_arrastar.mp3',
    'parabens'        => 'parabens.mp3',
];
foreach (['A', 'E', 'I', 'O', 'U'] as $letra) {
    $sons["letra$letra"] = "Letra $letra.m4a";
}
foreach ($nomes as $chave => $nome) {
    $sons[$chave] = "$nome.mp3";
}

function h(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function vogalDe(string $palavra): string
{
    return strtolower($palavra[0]);
}

/* Lista de botões "palavra + figura" das etapas 2 e 4 */
function listaPalavras(array $chaves, array $nomes): void
{ ?>
    <div class="lista-palavras">
        <?php foreach ($chaves as $k): ?>
            <button class="cartao btn-palavra" data-som="<?= $k ?>">
                <span><?= h($nomes[$k]) ?></span>
                <img src="../img/<?= $k ?>.png" alt="<?= h($nomes[$k]) ?>">
            </button>
        <?php endforeach; ?>
    </div>
<?php } ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - Atividade das Vogais</title>
    <link rel="icon" type="image/png" href="../img/logo.png">

    <!-- Permite arrastar e soltar com o dedo em celulares e tablets -->
    <script src="https://cdn.jsdelivr.net/npm/drag-drop-touch-polyfill@1.0.2/DragDropTouch.js"></script>

    <style>
        /* ===== Variáveis e base ===== */
        :root {
            --tinta: #1a1a1a;
            --amarelo: #fde05f;
            --amarelo-claro: #fff29c;
            --verde: #4cd964;
            --verde-claro: #b1e0a8;
            --vermelho: #ff5252;
            --roxo: #7e57c2;
            --branco-suave: rgb(255 255 255 / .85);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        [hidden] {
            display: none !important;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            padding: 72px 16px 24px;
            /* espaço no topo para o botão voltar */
            background: #f7f3cb;
            font-family: 'Fredoka', 'Comic Sans MS', sans-serif;
            overflow-x: hidden;
        }

        /* Botões usam a mesma fonte da página */
        button {
            font: inherit;
            color: inherit;
            cursor: pointer;
        }

        :focus-visible {
            outline: 4px solid var(--roxo);
            outline-offset: 3px;
        }

        /* ===== Fundo e botão voltar ===== */
        .fundo {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .circulo {
            position: absolute;
            width: var(--tam);
            aspect-ratio: 1;
            border-radius: 50%;
            background: var(--cor);
        }

        .circulo-azul {
            --tam: 260px;
            --cor: #a8c3d1;
            top: -50px;
            left: -50px;
        }

        .circulo-rosa {
            --tam: 300px;
            --cor: #f7d3ca;
            left: 22%;
            bottom: 18%;
        }

        .circulo-verde {
            --tam: 280px;
            --cor: var(--verde-claro);
            top: 12%;
            right: 18%;
        }

        .circulo-amarelo {
            --tam: 320px;
            --cor: #fce892;
            bottom: -80px;
            right: -50px;
        }

        .btn-voltar {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 20;
            display: grid;
            place-items: center;
            width: 45px;
            height: 45px;
            background: #fff;
            border: 2px solid var(--tinta);
            border-radius: 50%;
            box-shadow: 0 4px 0 var(--tinta);
            color: var(--tinta);
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
        }

        /* ===== Etapas ===== */
        .etapa {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1000px;
            margin: auto;
            /* centraliza sem cortar o topo quando o conteúdo é alto */
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .etapa-simples {
            max-width: 500px;
        }

        .explicacao {
            max-width: 600px;
            padding: 15px 25px;
            background: var(--branco-suave);
            border: 3px solid var(--tinta);
            border-radius: 25px;
            box-shadow: 0 5px 0 var(--tinta);
            font-size: clamp(1.125rem, .9rem + 1.2vw, 1.5rem);
            font-weight: bold;
            line-height: 1.3;
            text-align: center;
        }

        .explicacao p+p {
            margin-top: 1.1em;
        }

        /* Etapas 2 e 4: letra + som + lista de palavras */
        .conteudo-etapa {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 40px;
        }

        .lista-palavras {
            width: min(330px, 100%);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .grupo-letras {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: clamp(6px, 1.6vw, 12px);
        }

        /* Etapas 5 e 6: faixa de letras no topo, jogo logo abaixo */
        .painel-dropzones,
        .painel-letras {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: clamp(6px, 1.6vw, 20px);
        }

        .grid-figuras {
            display: grid;
            grid-template-columns: repeat(5, auto);
            justify-content: center;
            gap: 15px clamp(8px, 2vw, 20px);
        }

        .grid-palavras-jogo {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 300px));
            justify-content: center;
            gap: clamp(8px, 2vw, 20px);
        }

        /* ===== Cartões (base compartilhada) ===== */
        .cartao {
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--amarelo);
            border: 3px solid var(--tinta);
            border-radius: var(--raio, 25px);
            box-shadow: 0 var(--elevacao, 6px) 0 var(--tinta);
            color: #000;
            font-weight: 900;
            transition: transform .2s, background-color .2s, opacity .3s;
        }

        .card-letra {
            --tam: clamp(130px, 35vw, 210px);
            --raio: calc(var(--tam) * .17);
            --elevacao: 8px;
            width: var(--tam);
            height: var(--tam);
            font-size: calc(var(--tam) * .4);
        }

        .card-letra.vogais {
            font-size: calc(var(--tam) * .22);
            /* "AEIOU" cabe dentro do cartão */
        }

        .grupo-letras .card-letra {
            --tam: clamp(72px, 20vw, 210px);
        }

        .btn-palavra {
            --raio: 20px;
            justify-content: space-between;
            padding: 10px 20px;
            font-size: clamp(1.25rem, 4.5vw, 1.625rem);
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-palavra img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .btn-palavra:active {
            transform: scale(.98);
        }

        /* Etapa 5 */
        .card-drop {
            --tam: clamp(56px, min(14vw, 18vh), 150px);
            --raio: calc(var(--tam) * .2);
            --elevacao: 8px;
            width: var(--tam);
            height: var(--tam);
            font-size: calc(var(--tam) * .37);
        }

        .item-figura {
            --tam: clamp(54px, min(11vw, 13vh), 85px);
            --raio: 20px;
            --elevacao: 5px;
            width: var(--tam);
            height: var(--tam);
        }

        .item-figura img {
            width: 70%;
            height: 70%;
            object-fit: contain;
            pointer-events: none;
        }

        .item-figura.concluido {
            opacity: .2;
            pointer-events: none;
            box-shadow: none;
        }

        /* Etapa 6 */
        .card-letra-arrastavel {
            --tam: clamp(56px, min(14vw, 15vh), 120px);
            --raio: calc(var(--tam) * .25);
            --elevacao: 7px;
            width: var(--tam);
            height: var(--tam);
            font-size: calc(var(--tam) * .42);
        }

        .card-palavra-drop {
            justify-content: space-between;
            gap: 8px;
            height: clamp(52px, 9vh, 70px);
            padding: 0 clamp(8px, 2vw, 15px);
        }

        .card-palavra-drop>* {
            pointer-events: none;
            /* evita "piscar" ao arrastar sobre o texto/imagem */
        }

        .card-palavra-drop.concluido {
            background: var(--verde-claro);
        }

        .texto-palavra {
            min-width: 0;
            font-size: clamp(.875rem, 3.8vw, 1.625rem);
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .imagem-palavra {
            width: clamp(32px, 7vw, 48px);
            aspect-ratio: 1;
            object-fit: contain;
        }

        /* Itens arrastáveis */
        .item-figura,
        .card-letra-arrastavel {
            cursor: grab;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }

        .item-figura:active,
        .card-letra-arrastavel:active {
            cursor: grabbing;
            transform: scale(1.06);
        }

        /* Feedback do arraste (vêm por último para vencer os estilos base) */
        .cartao.hover-drop {
            transform: scale(1.04);
            background: var(--amarelo-claro);
        }

        .cartao.erro-arraste {
            animation: balancarErro .6s ease-in-out;
            background: rgb(255 82 82 / .35);
            border-color: var(--vermelho);
            box-shadow: 0 0 0 3px rgb(255 82 82 / .5);
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

        /* ===== Botões ===== */
        .btn-som {
            display: grid;
            place-items: center;
            width: 55px;
            height: 55px;
            background: transparent;
            border: 2px solid var(--tinta);
            border-radius: 50%;
            font-size: 1.6rem;
            transition: transform .2s;
        }

        .btn-avancar {
            width: 100%;
            max-width: 450px;
            height: 50px;
            background: transparent;
            border: 2px solid var(--tinta);
            border-radius: 30px;
            color: var(--tinta);
            font-size: 26px;
            font-weight: bold;
            transition: background-color .2s, color .2s;
        }

        .btn-avancar:disabled {
            opacity: .4;
            cursor: not-allowed;
            border-color: #888;
            color: #888;
        }

        @media (hover: hover) {
            .btn-som:hover {
                transform: scale(1.1);
            }

            .btn-avancar:hover:not(:disabled) {
                background: var(--tinta);
                color: #fff;
            }
        }

        /* ===== Tela de abertura (nuvens) ===== */
        #tela-transicao {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: url('../img/iniciando1.png') center / cover no-repeat;
            transition: opacity 1s ease-out, visibility 1s ease-out;
        }

        #tela-transicao.transicao-oculta {
            opacity: 0;
            visibility: hidden;
        }

        .nuvem-container {
            position: absolute;
            inset: 0 0 50% 0;
            overflow: hidden;
            pointer-events: none;
        }

        .nuvem {
            position: absolute;
            left: calc(var(--w) * -1.2);
            width: var(--w);
            height: calc(var(--w) * .32);
            background: rgb(255 255 255 / .85);
            border-radius: 50px;
            opacity: .8;
            animation: moverNuvem var(--dur) var(--atraso, 0s) linear infinite;
        }

        .nuvem::before,
        .nuvem::after {
            content: '';
            position: absolute;
            aspect-ratio: 1;
            background: rgb(255 255 255 / .85);
            border-radius: 50%;
        }

        .nuvem::before {
            width: calc(var(--w) * .36);
            top: calc(var(--w) * -.14);
            left: calc(var(--w) * .18);
        }

        .nuvem::after {
            width: calc(var(--w) * .28);
            top: calc(var(--w) * -.1);
            left: calc(var(--w) * .43);
        }

        .nuvem1 {
            --w: 140px;
            --dur: 8s;
            top: 15%;
        }

        .nuvem2 {
            --w: 180px;
            --dur: 11s;
            --atraso: 1s;
            top: 35%;
        }

        .nuvem3 {
            --w: 120px;
            --dur: 9.5s;
            --atraso: 2.5s;
            top: 8%;
        }

        @keyframes moverNuvem {
            to {
                transform: translateX(calc(100vw + 250px));
            }
        }

        /* ===== Tela de parabéns ===== */
        #tela-parabens {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: grid;
            place-items: center;
            padding-bottom: 10vh;
            background: #fbf5c8;
            overflow: hidden;
        }

        .conteudo-parabens-centro {
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .texto-parabens-colorido {
            display: flex;
            justify-content: center;
            gap: 6px;
            animation: pulsarLetras 1.5s infinite alternate ease-in-out;
        }

        .texto-parabens-colorido span {
            color: var(--cor);
            font-size: clamp(2.8rem, 9vw, 4.25rem);
            font-weight: 900;
            text-shadow: 2px 2px 0 #fff, -2px -2px 0 #fff, 2px -2px 0 #fff, -2px 2px 0 #fff,
                0 4px 6px rgb(0 0 0 / .15);
        }

        .mascote-parabens {
            width: clamp(150px, 30vw, 210px);
            height: auto;
            animation: flutuarMascote 2s infinite alternate ease-in-out;
        }

        .nuvem-parabens {
            position: absolute;
            z-index: 3;
            width: var(--larg);
            opacity: .9;
        }

        .nuvem-parabens-esq {
            --larg: clamp(120px, 24vw, 240px);
            top: 22%;
            left: 8%;
        }

        .nuvem-parabens-dir {
            --larg: clamp(130px, 26vw, 260px);
            top: 12%;
            right: 8%;
        }

        .gramado-parabens {
            position: absolute;
            z-index: 2;
            bottom: -50px;
            left: -5%;
            width: 110%;
            height: 180px;
            background: var(--verde);
            border-radius: 50% 50% 0 0;
        }

        @keyframes pulsarLetras {
            from {
                transform: scale(.95);
            }

            to {
                transform: scale(1.05);
            }
        }

        @keyframes flutuarMascote {
            to {
                transform: translateY(-10px);
            }
        }

        /* Confetes */
        .container-confetes {
            position: absolute;
            inset: 0;
            z-index: 4;
            overflow: hidden;
            pointer-events: none;
        }

        .confete {
            position: absolute;
            top: -20px;
            animation: cairConfete linear infinite;
            /* duração e atraso são definidos no JS */
        }

        .confete.retangulo {
            width: 12px;
            height: 20px;
            border-radius: 2px;
        }

        .confete.bolinha {
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
            from {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            to {
                transform: translateY(105vh) rotate(720deg) translateX(50px);
                opacity: .8;
            }
        }

        /* ===== Telas pequenas ===== */
        @media (max-width: 850px) {
            .conteudo-etapa {
                flex-direction: column;
                gap: 20px;
            }

            .circulo {
                opacity: .5;
            }
        }

        /* ===== Quem prefere menos movimento ===== */
        @media (prefers-reduced-motion: reduce) {

            .nuvem,
            .texto-parabens-colorido,
            .mascote-parabens {
                animation: none;
            }

            .confete {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Tela de abertura -->
    <div id="tela-transicao">
        <div class="nuvem-container">
            <div class="nuvem nuvem1"></div>
            <div class="nuvem nuvem2"></div>
            <div class="nuvem nuvem3"></div>
        </div>
    </div>

    <!-- Tela de parabéns -->
    <div id="tela-parabens" hidden>
        <div class="container-confetes" id="containerConfetes"></div>

        <img src="../img/nuvem_parabens.png" class="nuvem-parabens nuvem-parabens-esq" alt="" onerror="this.style.display='none'">
        <img src="../img/nuvem_parabens.png" class="nuvem-parabens nuvem-parabens-dir" alt="" onerror="this.style.display='none'">

        <div class="conteudo-parabens-centro">
            <div class="texto-parabens-colorido">
                <span style="--cor:#ff5252">P</span>
                <span style="--cor:#ff7043">A</span>
                <span style="--cor:#7e57c2">R</span>
                <span style="--cor:#26a69a">A</span>
                <span style="--cor:#fbc02d">B</span>
                <span style="--cor:#ec407a">É</span>
                <span style="--cor:#42a5f5">N</span>
                <span style="--cor:#00bcd4">S</span>
            </div>

            <img src="../img/lumiminho.png" alt="Lumi" class="mascote-parabens"
                onerror="this.onerror=null;this.src='../img/luminho.png'">
        </div>

        <div class="gramado-parabens"></div>
    </div>

    <a href="nivel1.php" class="btn-voltar" title="Voltar para a Trilha" aria-label="Voltar para a Trilha">←</a>

    <div class="fundo" aria-hidden="true">
        <div class="circulo circulo-azul"></div>
        <div class="circulo circulo-rosa"></div>
        <div class="circulo circulo-verde"></div>
        <div class="circulo circulo-amarelo"></div>
    </div>

    <!-- Etapa 1: introdução -->
    <section id="etapa1" class="etapa etapa-simples">
        <div class="explicacao">
            <p>As vogais são letras muito importantes para formar as palavras.</p>
            <p>Vamos aprender: <strong>A, E, I, O, U!</strong></p>
        </div>
        <button class="btn-avancar" aria-label="Próxima etapa">➔</button>
    </section>

    <!-- Etapa 2: vogal A -->
    <section id="etapa2" class="etapa" hidden>
        <div class="conteudo-etapa">
            <div class="cartao card-letra">Aa</div>
            <button class="btn-som" data-som="letraA" aria-label="Ouvir a letra A">🔊</button>
            <?php listaPalavras($etapa2, $nomes); ?>
        </div>
        <button class="btn-avancar" aria-label="Próxima etapa">➔</button>
    </section>

    <!-- Etapa 3: as outras vogais -->
    <section id="etapa3" class="etapa" hidden>
        <div class="explicacao">
            <p>Muito bem!</p>
            <p>Agora vamos conhecer as outras vogais:</p>
            <p><strong>E - I - O - U</strong></p>
            <p>Elas aparecem em muitas palavras!</p>
        </div>

        <div class="grupo-letras">
            <?php foreach (['Ee', 'Ii', 'Oo', 'Uu'] as $letra): ?>
                <div class="cartao card-letra"><?= $letra ?></div>
            <?php endforeach; ?>
        </div>
        <button class="btn-avancar" aria-label="Próxima etapa">➔</button>
    </section>

    <!-- Etapa 4: todas as vogais -->
    <section id="etapa4" class="etapa" hidden>
        <div class="conteudo-etapa">
            <div class="cartao card-letra vogais">AEIOU</div>
            <button class="btn-som" data-som="instrucao" aria-label="Ouvir a instrução">🔊</button>
            <?php listaPalavras($etapa4, $nomes); ?>
        </div>
        <button class="btn-avancar" aria-label="Próxima etapa">➔</button>
    </section>

    <!-- Etapa 5: arrastar a figura para a vogal inicial -->
    <section id="etapa5" class="etapa" hidden>
        <div class="explicacao">
            <p><strong>Vamos brincar!</strong><br>Arraste cada figura para a vogal que começa o nome dela.</p>
        </div>

        <div class="painel-dropzones">
            <?php foreach ($vogais as $v): ?>
                <div class="cartao card-drop" data-correta="<?= $v ?>"><?= strtoupper($v) . $v ?></div>
            <?php endforeach; ?>
            <button class="btn-som" data-som="instrucao" aria-label="Ouvir a instrução">🔊</button>
        </div>

        <div class="grid-figuras">
            <?php foreach ($etapa5 as $k): ?>
                <div class="cartao item-figura" id="fig-<?= $k ?>" draggable="true" data-letra="<?= vogalDe($nomes[$k]) ?>">
                    <img src="../img/<?= $k ?>.png" alt="<?= h($nomes[$k]) ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <button id="btn-avancar-etapa5" class="btn-avancar" aria-label="Próxima etapa" disabled>➔</button>
    </section>

    <!-- Etapa 6: arrastar a vogal para completar a palavra -->
    <section id="etapa6" class="etapa" hidden>
        <div class="painel-letras">
            <?php foreach ($vogais as $v): ?>
                <div class="cartao card-letra-arrastavel" id="letra-<?= $v ?>" draggable="true" data-letra="<?= $v ?>">
                    <?= strtoupper($v) . $v ?>
                </div>
            <?php endforeach; ?>
            <button class="btn-som" data-som="instrucao" title="Ouvir instrução" aria-label="Ouvir a instrução">🔊</button>
        </div>

        <div class="grid-palavras-jogo">
            <?php foreach ($etapa6 as $k):
                $nome  = $nomes[$k];
                $resto = substr($nome, 1); // a palavra sem a vogal inicial
            ?>
                <div class="cartao card-palavra-drop" data-correta="<?= vogalDe($nome) ?>"
                    data-resto="<?= h($resto) ?>" data-audio="<?= $k ?>">
                    <span class="texto-palavra">_<?= h($resto) ?></span>
                    <img src="../img/<?= $k ?>.png" alt="<?= h($nome) ?>" class="imagem-palavra">
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Áudios -->
    <audio id="som-iniciando" src="../audios/iniciando_fase.mp3" autoplay></audio>
    <?php foreach ($sons as $id => $arquivo): ?>
        <audio id="som-<?= $id ?>" src="../audios/<?= h($arquivo) ?>"></audio>
    <?php endforeach; ?>
    <audio id="som-fundo" loop preload="auto">
        <source src="../audios/fundoatv.mp3" type="audio/mpeg">
    </audio>

    <script>
        const qsa = seletor => document.querySelectorAll(seletor);

        /* ---------- Tela de abertura ---------- */
        setTimeout(() => {
            document.getElementById('tela-transicao').classList.add('transicao-oculta');
        }, 4000);

        /* ---------- Sons ---------- */
        function tocarSom(nome) {
            const audio = document.getElementById('som-' + nome);
            if (!audio) return;
            audio.currentTime = 0;
            audio.play().catch(() => {});
        }

        // Qualquer elemento com data-som="nome" toca o áudio ao ser clicado
        document.addEventListener('click', e => {
            const botao = e.target.closest('[data-som]');
            if (botao) tocarSom(botao.dataset.som);
        });

        /* ---------- Etapas ---------- */
        let etapaAtual = 1;

        function avancar() {
            document.getElementById('etapa' + etapaAtual).hidden = true;
            etapaAtual++;
            document.getElementById('etapa' + etapaAtual).hidden = false;

            if (etapaAtual === 6) tocarSom('instrucao');
        }

        qsa('.btn-avancar').forEach(botao => botao.addEventListener('click', avancar));

        /* ---------- Arrastar e soltar (comum às etapas 5 e 6) ---------- */
        function arrastar(e) {
            e.dataTransfer.setData('text/plain', e.currentTarget.id);
        }

        function permitirSoltar(e) {
            e.preventDefault();
            if (!e.currentTarget.classList.contains('concluido')) {
                e.currentTarget.classList.add('hover-drop');
            }
        }

        function sairDrop(e) {
            e.currentTarget.classList.remove('hover-drop');
        }

        function mostrarErro(zona) {
            zona.classList.remove('erro-arraste');
            void zona.offsetWidth; // reinicia a animação
            zona.classList.add('erro-arraste');
            tocarSom('tentarNovamente');
            setTimeout(() => zona.classList.remove('erro-arraste'), 1500);
        }

        // Confere se a vogal do item arrastado é a da zona.
        // Acertou: toca o som e devolve o item arrastado. Errou: mostra o erro e devolve null.
        function validar(e) {
            e.preventDefault();
            const zona = e.currentTarget;
            zona.classList.remove('hover-drop');

            const item = document.getElementById(e.dataTransfer.getData('text/plain'));
            if (!item || zona.classList.contains('concluido')) return null;

            if (item.dataset.letra !== zona.dataset.correta) {
                tocarSom('erro');
                mostrarErro(zona);
                return null;
            }

            tocarSom('acerto');
            return item;
        }

        function ligarArraste(itens, zonas, aoSoltar) {
            itens.forEach(item => item.addEventListener('dragstart', arrastar));
            zonas.forEach(zona => {
                zona.addEventListener('dragover', permitirSoltar);
                zona.addEventListener('dragleave', sairDrop);
                zona.addEventListener('drop', aoSoltar);
            });
        }

        /* ---------- Etapa 5: figura -> vogal ---------- */
        const TOTAL_FIGURAS = qsa('.item-figura').length;
        let acertosEtapa5 = 0;

        function soltarFigura(e) {
            const figura = validar(e);
            if (!figura) return;

            figura.classList.add('concluido');
            figura.draggable = false;

            if (++acertosEtapa5 === TOTAL_FIGURAS) {
                document.getElementById('btn-avancar-etapa5').disabled = false;
            }
        }

        /* ---------- Etapa 6: vogal -> palavra ---------- */
        const TOTAL_PALAVRAS = qsa('.card-palavra-drop').length;
        let acertosEtapa6 = 0;

        function soltarLetra(e) {
            const zona = e.currentTarget;
            if (!validar(e)) return;

            zona.querySelector('.texto-palavra').textContent =
                zona.dataset.correta.toUpperCase() + zona.dataset.resto;
            zona.classList.add('concluido');

            setTimeout(() => tocarSom(zona.dataset.audio), 500);

            if (++acertosEtapa6 === TOTAL_PALAVRAS) {
                setTimeout(finalizarAtividade, 1000);
            }
        }

        ligarArraste(qsa('.item-figura'), qsa('.card-drop'), soltarFigura);
        ligarArraste(qsa('.card-letra-arrastavel'), qsa('.card-palavra-drop'), soltarLetra);

        /* ---------- Final da atividade ---------- */
        function finalizarAtividade() {
            fetch('vogais.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'concluir_fase=12'
                })
                .catch(() => {}) // mesmo sem salvar, mostra os parabéns
                .finally(() => setTimeout(mostrarParabens, 600));
        }

        function mostrarParabens() {
            document.getElementById('tela-parabens').hidden = false;
            gerarConfetes();
            tocarSom('parabens');

            setTimeout(() => {
                window.location.href = 'nivel1.php';
            }, 4500);
        }

        function gerarConfetes() {
            const cores = ['#ff5252', '#ff7043', '#fbc02d', '#9ccc65', '#26a69a', '#42a5f5', '#7e57c2', '#ec407a'];
            const formatos = ['retangulo', 'bolinha', 'serpentina'];
            const sortear = lista => lista[Math.floor(Math.random() * lista.length)];
            const container = document.getElementById('containerConfetes');

            for (let i = 0; i < 75; i++) {
                const confete = document.createElement('div');
                confete.className = `confete ${sortear(formatos)}`;
                Object.assign(confete.style, {
                    backgroundColor: sortear(cores),
                    left: `${Math.random() * 100}%`,
                    animationDuration: `${2.5 + Math.random() * 3}s`,
                    animationDelay: `${Math.random() * 2}s`
                });
                container.append(confete);
            }
        }

        /* ---------- Música de fundo (continua de onde parou) ---------- */
        const musica = document.getElementById('som-fundo');
        musica.volume = 0.2;
        musica.currentTime = parseFloat(localStorage.getItem('musica_tempo')) || 0;

        musica.play().catch(() => {
            // o navegador bloqueou o autoplay: toca no primeiro clique
            document.addEventListener('click', () => musica.play().catch(() => {}), {
                once: true
            });
        });

        musica.addEventListener('timeupdate', () => {
            localStorage.setItem('musica_tempo', musica.currentTime);
        });
    </script>
</body>

</html>