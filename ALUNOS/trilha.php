<?php
session_start();
$aluno_logado = isset($_SESSION['aluno_id']);
$nome_aluno = $aluno_logado ? $_SESSION['aluno_nome'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - Trilha do Saber</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/trilha.css">
</head>
<body>

<audio id="somFundo" loop preload="auto">
    <source src="audios/fundolumi.mp3" type="audio/mpeg">
</audio>

<script>
    const audio = document.getElementById('somFundo');
    audio.volume = 0.3;

    const tempoSalvo = localStorage.getItem('musica_tempo');
    if (tempoSalvo) {
        audio.currentTime = parseFloat(tempoSalvo);
    }

    function tocarMusica() {
        audio.play().catch(() => {
            document.addEventListener('click', () => {
                audio.play();
            }, { once: true });
        });
    }

    tocarMusica();

    audio.addEventListener('timeupdate', () => {
        localStorage.setItem('musica_tempo', audio.currentTime);
    });

    window.addEventListener('beforeunload', () => {
        localStorage.setItem('musica_tempo', audio.currentTime);
    });
</script>

    <div id="trilha-container">
        <div class="nuvens-container">
            <img src="img/Nuvens.png" alt="Nuvens" class="nuvem nuvem1">
            <img src="img/Nuvens.png" alt="Nuvens" class="nuvem nuvem2">
        </div>

<header class="trilha-header">
    <a href="index.php" class="btn-voltar">⬅ VOLTAR</a>

    <a href="login.php" class="btn-login-img">
        <img src="img/login.png" alt="Login">
    </a>
    
</header>

        <button class="carrossel-btn btn-prev" onclick="mudarCard(-1)">❮</button>

        <div class="carrossel-wrapper">
            <div class="carrossel-track" id="track">

<div class="card-atividade ativo">
    <div class="card-badge">Nível 1</div>
    <div class="card-imagem">
        <img src="img/n1.png" alt="Casinha N1" onerror="this.src='img/logo.png'">
    </div>
    <h3>O ALFABETO</h3>
    <a href="nivel1/nivel1.php" class="btn-jogar">COMEÇAR</a>
</div>

<div class="card-atividade">
    <div class="card-badge">Nível 2</div>
    <div class="card-imagem">
        <img src="img/n2.png" alt="Casinha N2" onerror="this.src='img/logo.png'">
    </div>
    <h3>AS SÍLABAS FÁCEIS</h3>
    <a href="nivel2/nivel2.php" class="btn-jogar">COMEÇAR</a>
</div>

<div class="card-atividade">
    <div class="card-badge">Nível 3</div>
    <div class="card-imagem">
        <img src="img/n3.png" alt="Casinha N3" onerror="this.src='img/logo.png'">
    </div>
    <h3>AS SÍLABAS COMPLEXAS</h3>
    <a href="nivel3/nivel3.php" class="btn-jogar">COMEÇAR</a>
</div>

<div class="card-atividade">
    <div class="card-badge">Nível 4</div>
    <div class="card-imagem">
        <img src="img/n4.png" alt="Casinha N4" onerror="this.src='img/logo.png'">
    </div>
    <h3>AS PALAVRAS</h3>
    <a href="nivel4/nivel4.php" class="btn-jogar">COMEÇAR</a>
</div>

<div class="card-atividade">
    <div class="card-badge">Nível 5</div>
    <div class="card-imagem">
        <img src="img/n5.png" alt="Casinha N5" onerror="this.src='img/logo.png'">
    </div>
    <h3>OS FONEMAS</h3>
    <a href="nivel5/nivel5.php" class="btn-jogar">COMEÇAR</a>
</div>
            </div>
        </div>

        <button class="carrossel-btn btn-next" onclick="mudarCard(1)">❯</button>

        <div class="carrossel-indicadores" id="indicadores">
            <span class="dot ativo" onclick="irParaCard(0)"></span>
            <span class="dot" onclick="irParaCard(1)"></span>
            <span class="dot" onclick="irParaCard(2)"></span>
            <span class="dot" onclick="irParaCard(3)"></span>
            <span class="dot" onclick="irParaCard(4)"></span>
        </div>
    </div>

    <script>
        let indexAtual = 0;
        const cards = document.querySelectorAll('.card-atividade');
        const dots = document.querySelectorAll('.dot');
        const track = document.getElementById('track');

        function atualizarCarrossel() {
            const larguraCard = cards[0].offsetWidth + 30;
            track.style.transform = `translateX(${-indexAtual * larguraCard}px)`;

            cards.forEach((card, i) => {
                card.classList.toggle('ativo', i === indexAtual);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('ativo', i === indexAtual);
            });
        }

        function mudarCard(direcao) {
            indexAtual += direcao;
            if (indexAtual < 0) indexAtual = 0;
            if (indexAtual >= cards.length) indexAtual = cards.length - 1;
            atualizarCarrossel();
        }

        function irParaCard(index) {
            indexAtual = index;
            atualizarCarrossel();
        }
    </script>

</body>
</html>