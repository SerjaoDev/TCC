<?php
session_start();
require_once '../conexao.php';

$estrutura_id = 5;
$total_niveis = 15;
$titulo = 'OS FONEMAS';
$fundo = 'fn5.png';
$cor_linha = '#4CAF50';

$atividades_arquivos = [
    1  => 'atv1n5.php',
    2  => 'atv2n5.php',
    3  => 'atv3n5.php',
    4  => 'atv4n5.php',
    5  => 'atv5n5.php',
    6  => 'atv6n5.php',
    7  => 'atv7n5.php',
    8  => 'atv8n5.php',
    9  => 'atv9n5.php',
    10 => 'atv10n5.php',
    11 => 'atv11n5.php',
    12 => 'atv12n5.php',
    13 => 'atv13n5.php',
    14 => 'atv14n5.php',
    15 => 'atv15n5.php',
    16 => 'atv16n5.php',
    17 => 'atv17n5.php',
    18 => 'atv18n5.php',
    19 => 'atv19n5.php',
    20 => 'atv20n5.php',                    
];

$aluno_id = $_SESSION['aluno_id'] ?? 1;
$fasesConcluidas = [];

if ($aluno_id) {
    try {
        $stmt = $pdo->prepare("SELECT nivel_atual FROM progresso WHERE aluno_id = :aluno_id AND estrutura_id = :estrutura_id");
        $stmt->execute([
            ':aluno_id'     => $aluno_id,
            ':estrutura_id' => $estrutura_id
        ]);
        $fasesConcluidas = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    } catch (PDOException $e) {
        $fasesConcluidas = [];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - <?= $titulo ?></title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/fases.css">
    <style>
        html, body { 
            background: url('../img/<?= $fundo ?>') no-repeat center top / cover !important;
            background-attachment: fixed !important;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .fases-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 60px 0 100px 0;
        }

        .trilha-vertical {
            position: relative;
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .svg-circuito {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .no-lampada {
            position: relative;
            z-index: 2;
            margin: 50px -70px;
        }

        .lampada-desativada {
            cursor: not-allowed;
            opacity: 0.6;
            filter: grayscale(100%);
        }

        .linha-conecta { 
            stroke: <?= $cor_linha ?>; 
            stroke-width: 10;
            stroke-dasharray: 0, 25;
            stroke-linecap: round;
            fill: none;
        }

        .linha-apagada {
            stroke: #aaa;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="fases-container">
        <a href="../trilha.php" class="btn-voltar-circulo" title="Voltar"><span>←</span></a>
        <div class="trilha-vertical">
            <svg class="svg-circuito" id="svg-circuito"></svg>
            <?php for ($i = $total_niveis; $i >= 1; $i--): ?>
                <?php 
                    $acesa = ($i === 1) || in_array($i - 1, $fasesConcluidas);
                    $classe_posicao = ($i % 2 == 0) ? 'pos-direita' : 'pos-esquerda';
                    $link_destino = $atividades_arquivos[$i] ?? "atv{$i}n5.php";
                ?>
                <div class="no-lampada <?= $classe_posicao ?> <?= $acesa ? 'acesa' : 'apagada' ?>" data-fase="<?= $i ?>">
                    <?php if ($acesa): ?>
                        <a href="<?= $link_destino ?>" class="link-lampada" title="Ir para Atividade <?= $i ?>">
                            <img src="../img/lampada_acesa.png" alt="Atividade <?= $i ?>" class="img-lampada" onerror="this.src='../img/logo.png'">
                            <span class="num-fase"><?= $i ?></span>
                        </a>
                    <?php else: ?>
                        <div class="lampada-desativada" title="Complete a atividade <?= $i - 1 ?> para desbloquear!">
                            <img src="../img/lampada_apagada.png" alt="Atividade <?= $i ?> Apagada" class="img-lampada" onerror="this.src='../img/logo.png'">
                            <span class="num-fase"><?= $i ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
            <div class="placa-titulo"><h2><?= $titulo ?></h2></div>
        </div>
    </div>
    <script>
        function conectarLampadas() {
            const svg = document.getElementById('svg-circuito');
            const lampadas = Array.from(document.querySelectorAll('.no-lampada')).reverse();
            svg.innerHTML = '';
            for (let i = 0; i < lampadas.length - 1; i++) {
                const atual = lampadas[i];
                const proxima = lampadas[i + 1];
                const rect1 = atual.getBoundingClientRect();
                const rect2 = proxima.getBoundingClientRect();
                const containerRect = document.querySelector('.trilha-vertical').getBoundingClientRect();
                const x1 = (rect1.left + rect1.width / 2) - containerRect.left;
                const y1 = (rect1.top + rect1.height / 2) - containerRect.top;
                const x2 = (rect2.left + rect2.width / 2) - containerRect.left;
                const y2 = (rect2.top + rect2.height / 2) - containerRect.top;
                const meioY = (y1 + y2) / 2;
                const d = `M ${x1},${y1} C ${x1},${meioY} ${x2},${meioY} ${x2},${y2}`;
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', d);
                const ehAcesa = atual.classList.contains('acesa') && proxima.classList.contains('acesa');
                path.setAttribute('class', 'linha-conecta ' + (ehAcesa ? 'linha-acesa' : 'linha-apagada'));
                svg.appendChild(path);
            }
        }
        window.addEventListener('load', () => {
            conectarLampadas();
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        });
        window.addEventListener('resize', conectarLampadas);
    </script>
</body>
</html>