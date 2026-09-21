<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];
$stmt = $pdo->prepare("SELECT nivel_atual FROM progresso WHERE aluno_id = :id");
$stmt->execute([':id' => $aluno_id]);
$progresso = $stmt->fetch(PDO::FETCH_ASSOC);

$nivel_atual = $progresso['nivel_atual'] ?? 1;

$estruturas = [
    1 => ['nome' => 'O Alfabeto', 'cor' => '#6EC1E4', 'icone' => '🏫'],
    2 => ['nome' => 'As Sílabas Fáceis', 'cor' => '#3498DB', 'icone' => '🏭'],
    3 => ['nome' => 'As Sílabas Complexas', 'cor' => '#9B59B6', 'icone' => '🏰'],
    4 => ['nome' => 'As Palavras', 'cor' => '#8E44AD', 'icone' => '🏪'],
    5 => ['nome' => 'Os Fonemas', 'cor' => '#FD79A8', 'icone' => '🎙️']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - Estruturas</title>
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

<div class="mobile-wrapper">
    <div class="estruturas-bg">
        <h3 style="text-align: center; color: #2D3436; margin-bottom: 20px;">Selecione uma Estrutura</h3>

        <?php foreach ($estruturas as $num => $e): ?>
            <a href="trilha.php?estrutura=<?php echo $num; ?>" class="estrutura-card ativa">
                <div style="font-size: 2.5rem;"><?php echo $e['icone']; ?></div>
                <div style="flex-grow: 1;">
                    <span class="banner-nivel">Nível <?php echo $num; ?></span>
                    <h4 style="margin-top: 5px;"><?php echo $e['nome']; ?></h4>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>