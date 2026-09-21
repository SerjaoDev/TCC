<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/style.css">
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

    <div id="splash-screen">
        <img src="img/logo.png" alt="Lumi Logo" class="splash-logo">
    </div>

    <div id="main-screen">
        <div class="nuvens-container">
            <img src="img/Nuvens.png" alt="Nuvens" class="nuvem nuvem1">
            <img src="img/Nuvens.png" alt="Nuvens" class="nuvem nuvem2">
        </div>

        <img src="img/estrelas.png" class="estrela est-1" alt="Estrela">
        <img src="img/estrelas.png" class="estrela est-2" alt="Estrela">
        <img src="img/estrelas.png" class="estrela est-3" alt="Estrela">
        <img src="img/estrelas.png" class="estrela est-4" alt="Estrela">

         <div class="lado-esquerdo">
    <img src="img/letreiro.png" alt="LUMI" class="letreiro-img">
    <img src="img/mascote.png" alt="Mascote Lumi" class="mascote-flutuante">
    <a href="trilha.php">
        <img src="img/Botao.png" alt="Começar" class="btn-comecar" onerror="this.onerror=null; this.src='img/Botão.png';">
    </a>
</div>

        <div class="lado-direito">
            <a href="login.php">
                <img src="img/Botao.png" alt="Começar" class="btn-comecar" onerror="this.onerror=null; this.src='img/Botão.png';">
            </a>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const splash = document.getElementById('splash-screen');
            const main = document.getElementById('main-screen');
            
            splash.style.opacity = '0';
            setTimeout(() => {
                splash.style.display = 'none';
                main.classList.add('ativo');
            }, 800);
        }, 3000);
    </script>

</body>
</html>