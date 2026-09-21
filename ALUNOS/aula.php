<?php
$nivel = $_GET['nivel'] ?? 1;
$etapa = $_GET['etapa'] ?? 1;

$nomes_niveis = [
    1 => 'O ALFABETO',
    2 => 'AS SÍLABAS FÁCEIS',
    3 => 'AS SÍLABAS COMPLEXAS',
    4 => 'AS PALAVRAS',
    5 => 'OS FONEMAS'
];

$nome_nivel = $nomes_niveis[$nivel] ?? 'Nível Indefinido';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula - <?= $nome_nivel ?> (Etapa <?= $etapa ?>)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Fredoka', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card-aula {
            background: #ffffff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .badge-nivel {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0284c7;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        h1 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 8px;
        }

        h2 {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 25px;
        }

        .area-conteudo {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 40px 20px;
            margin-bottom: 25px;
        }

        .btn-voltar {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .btn-voltar:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>

    <div class="card-aula">
        <span class="badge-nivel">Nível <?= $nivel ?></span>
        <h1><?= $nome_nivel ?></h1>
        <h2>Aula - Etapa <?= $etapa ?> de 10</h2>

        <div class="area-conteudo">
            <p>Conteúdo da aula em desenvolvimento...</p>
        </div>

        <a href="nivel<?= $nivel ?>.php" class="btn-voltar">← Voltar para o Nível <?= $nivel ?></a>
    </div>

</body>
</html>