<?php
session_start();
require_once '../conexao.php';

$nivel_atual  = 1;
$total_niveis = 15;
$estrutura_id = 1;
$titulo       = 'O ALFABETO';
$fundo        = 'fn1.png';
$cor_linha    = '#2196F3';

$atividades_arquivos = [
    1  => 'atv1n1.php',
    2  => 'atv2n1.php',
    3  => 'atv3n1.php',
    4  => 'atv4n1.php',
    5  => 'atv5n1.php',
    6  => 'atv6n1.php',
    7  => 'atv7n1.php',
    8  => 'atv8n1.php',
    9  => 'atv9n1.php',
    10 => 'atv10n1.php',
    11 => 'atv11n1.php',
    12 => 'atv12n1.php',
    13 => 'atv13n1.php',
    14 => 'atv14n1.php',
    15 => 'atv15n1.php',
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
    <title>LUMI - <?= htmlspecialchars($titulo) ?></title>
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
</script>

    <div class="fases-container">

        <a href="../trilha.php" class="btn-voltar-circulo" title="Voltar"><span>←</span></a>
        
        <div class="trilha-vertical" id="trilha-vertical">
            <svg class="svg-circuito" id="svg-circuito"></svg>
            
            <?php for ($i = $total_niveis; $i >= 1; $i--): ?>
                <?php 
                    $acesa = ($i === 1) || in_array($i - 1, $fasesConcluidas);
                    $classe_posicao = ($i % 2 == 0) ? 'pos-direita' : 'pos-esquerda';
                    $link_destino = $atividades_arquivos[$i] ?? "atv{$i}n1.php";
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
            
            <div class="placa-titulo"><h2><?= htmlspecialchars($titulo) ?></h2></div>
        </div>
    </div>


    <script>
        function conectarLampadas() {
            const svg = document.getElementById('svg-circuito');
            const container = document.getElementById('trilha-vertical');
            const lampadas = Array.from(document.querySelectorAll('.no-lampada')).reverse();
            
            if (!container || lampadas.length === 0) return;

            svg.innerHTML = '';
            const containerRect = container.getBoundingClientRect();

            for (let i = 0; i < lampadas.length - 1; i++) {
                const atual = lampadas[i];
                const proxima = lampadas[i + 1];
                
                const rect1 = atual.getBoundingClientRect();
                const rect2 = proxima.getBoundingClientRect();
                
                const x1 = (rect1.left + rect1.width / 2) - containerRect.left;
                const y1 = (rect1.top + rect1.height / 2) - containerRect.top;
                const x2 = (rect2.left + rect2.width / 2) - containerRect.left;
                const y2 = (rect2.top + rect2.height / 2) - containerRect.top;
                
                const meioY = (y1 + y2) / 2;
                const d = `M ${x1},${y1} C ${x1},${meioY} ${x2},${meioY} ${x2},${y2}`;
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', d);
                path.setAttribute('stroke-dasharray', '0, 18');
                
                const ehAcesa = atual.classList.contains('acesa') && proxima.classList.contains('acesa');
                path.setAttribute('class', 'linha-conecta ' + (ehAcesa ? 'linha-acesa' : 'linha-apagada'));
                
                svg.appendChild(path);
            }
        }

        window.addEventListener('load', () => {
            setTimeout(conectarLampadas, 100);
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        });

        window.addEventListener('resize', conectarLampadas);
    </script>
</body>
</html>