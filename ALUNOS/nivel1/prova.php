<?php
session_start();
require_once '../conexao.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); $aluno_id = $_SESSION['aluno_id'] ?? 1; if
($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_prova'])) { header('Content-Type: application/json;
charset=utf-8'); $nota = isset($_POST['nota']) ? (int) $_POST['nota'] : 0; $total = isset($_POST['total']) ? (int)
$_POST['total'] : 20; if ($nota < 0) { $nota = 0; } if ($nota > $total) { $nota = $total; } $percentual = $total > 0 ?
round(($nota / $total) * 100, 2) : 0; try { $stmt = $pdo->prepare(" INSERT INTO provas (aluno_id, nota, total,
percentual) VALUES (:aluno_id, :nota, :total, :percentual) "); $stmt->execute([ ':aluno_id' => $aluno_id, ':nota' =>
$nota, ':total' => $total, ':percentual' => $percentual ]); echo json_encode([ 'status' => 'sucesso', 'nota' => $nota,
'total' => $total, 'percentual' => $percentual ]); } catch (PDOException $e) { echo json_encode([ 'status' => 'erro',
'mensagem' => $e->getMessage() ]); } exit; } ?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>LUMI - Prova Final do Alfabeto</title>

    <link rel="icon" type="image/png" href="../img/logo.png" />

    <style>
      * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
      }

      body {
          font-family: 'Fredoka', 'Comic Sans MS', sans-serif;
          min-height: 100vh;
          background: #f7f3cb;
          color: #1a1a1a;
          overflow-x: hidden;
      }

      .fundo {
          position: fixed;
          inset: 0;
          overflow: hidden;
          pointer-events: none;
          z-index: 0;
      }

      .bolha {
          position: absolute;
          border-radius: 50%;
      }

      .bolha1 {
          width: 280px;
          height: 280px;
          background: #b1e0a8;
          top: -80px;
          right: 10%;
      }

      .bolha2 {
          width: 300px;
          height: 300px;
          background: #f7d3ca;
          bottom: -100px;
          left: 10%;
      }

      .bolha3 {
          width: 220px;
          height: 220px;
          background: #a8c3d1;
          top: 30%;
          left: -100px;
      }

      .bolha4 {
          width: 250px;
          height: 250px;
          background: #fce892;
          right: -80px;
          bottom: 10%;
      }

      .btn-voltar {
          position: fixed;
          top: 20px;
          left: 20px;
          z-index: 100;

          width: 48px;
          height: 48px;

          border: 3px solid #1a1a1a;
          border-radius: 50%;

          background: white;

          display: flex;
          align-items: center;
          justify-content: center;

          text-decoration: none;

          color: #1a1a1a;
          font-size: 24px;
          font-weight: bold;

          box-shadow: 0 5px 0 #1a1a1a;

          transition: .2s;
      }

      .btn-voltar:hover {
          transform: translateY(-2px);
      }

      .pagina {
          position: relative;
          z-index: 5;

          min-height: 100vh;

          display: flex;
          justify-content: center;
          align-items: center;

          padding: 35px 20px;
      }

      .prova {
          width: 100%;
          max-width: 900px;

          display: flex;
          flex-direction: column;
          align-items: center;
      }

      .cabecalho {
          width: 100%;
          max-width: 750px;
          text-align: center;

          margin-bottom: 20px;
      }

      .titulo {
          font-size: 42px;
          font-weight: 900;

          color: #ff5252;

          text-shadow:
              2px 2px 0 white,
              -2px -2px 0 white,
              2px -2px 0 white,
              -2px 2px 0 white;
      }

      .subtitulo {
          font-size: 22px;
          font-weight: bold;

          margin-top: 5px;
      }

      .progresso-area {
          width: 100%;
          max-width: 700px;

          margin-bottom: 25px;
      }

      .progresso-info {
          display: flex;
          justify-content: space-between;
          align-items: center;

          font-size: 18px;
          font-weight: bold;

          margin-bottom: 8px;
      }

      .barra {
          width: 100%;
          height: 18px;

          background: white;

          border: 3px solid #1a1a1a;
          border-radius: 20px;

          overflow: hidden;
      }

      .barra-progresso {
          width: 0%;
          height: 100%;

          background: #4cd964;

          transition: width .4s ease;
      }

      .card-questao {
          width: 100%;
          max-width: 760px;

          background: rgba(255, 255, 255, .94);

          border: 4px solid #1a1a1a;
          border-radius: 35px;

          padding: 30px;

          box-shadow: 0 9px 0 #1a1a1a;

          text-align: center;
      }

      .numero-questao {
          font-size: 18px;
          font-weight: bold;
          color: #7e57c2;

          margin-bottom: 8px;
      }

      .instrucao {
          font-size: 27px;
          font-weight: 900;

          margin-bottom: 20px;
      }

      .btn-audio {
          width: 72px;
          height: 72px;

          border-radius: 50%;

          border: 3px solid #1a1a1a;

          background: #fde05f;

          cursor: pointer;

          display: flex;
          align-items: center;
          justify-content: center;

          margin: 0 auto 20px;

          box-shadow: 0 5px 0 #1a1a1a;

          transition: .15s;
      }

      .btn-audio:hover {
          transform: scale(1.05);
      }

      .btn-audio:active {
          transform: translateY(4px);
          box-shadow: 0 1px 0 #1a1a1a;
      }

      .btn-audio svg {
          width: 34px;
          height: 34px;
      }

      .imagem-questao {
          width: 150px;
          height: 150px;

          object-fit: contain;

          margin: 5px auto 20px;

          display: block;
      }

      .letra-grande {
          width: 180px;
          height: 180px;

          margin: 0 auto 20px;

          background: #fde05f;

          border: 4px solid #1a1a1a;
          border-radius: 35px;

          display: flex;
          justify-content: center;
          align-items: center;

          box-shadow: 0 8px 0 #1a1a1a;

          font-size: 85px;
          font-weight: 900;
      }

      .palavra-grande {
          font-size: 48px;
          font-weight: 900;
          letter-spacing: 4px;

          margin: 15px 0 25px;
      }

      .lacuna {
          color: #ff5252;
      }

      .alternativas {
          display: grid;

          grid-template-columns: repeat(2, 1fr);

          gap: 15px;

          width: 100%;
          max-width: 580px;

          margin: 0 auto;
      }

      .alternativa {
          min-height: 70px;

          border: 3px solid #1a1a1a;
          border-radius: 22px;

          background: #fde05f;

          box-shadow: 0 6px 0 #1a1a1a;

          cursor: pointer;

          font-family: inherit;

          font-size: 30px;
          font-weight: 900;

          transition: .15s;

          display: flex;
          justify-content: center;
          align-items: center;
      }

      .alternativa:hover:not(:disabled) {
          transform: translateY(-3px);
          background: #fff29c;
      }

      .alternativa:active:not(:disabled) {
          transform: translateY(3px);
          box-shadow: 0 2px 0 #1a1a1a;
      }

      .alternativa:disabled {
          cursor: default;
      }

      .alternativa.correta {
          background: #4cd964 !important;
          color: #000;
      }

      .alternativa.errada {
          background: #ff8a80 !important;
      }

      .feedback {
          min-height: 42px;

          margin-top: 20px;

          font-size: 22px;
          font-weight: 900;
      }

      .feedback.acerto {
          color: #22a447;
      }

      .feedback.erro {
          color: #e53935;
      }

      .btn-proxima {
          width: 100%;
          max-width: 580px;

          height: 55px;

          margin-top: 15px;

          border: 3px solid #1a1a1a;
          border-radius: 30px;

          background: #42a5f5;

          color: white;

          font-family: inherit;

          font-size: 25px;
          font-weight: 900;

          cursor: pointer;

          box-shadow: 0 6px 0 #1a1a1a;

          transition: .15s;
      }

      .btn-proxima:hover {
          transform: translateY(-2px);
      }

      .btn-proxima:active {
          transform: translateY(3px);
          box-shadow: 0 2px 0 #1a1a1a;
      }

      .btn-proxima:disabled {
          display: none;
      }

      #resultado {
          position: fixed;

          inset: 0;

          z-index: 1000;

          background: #fbf5c8;

          display: none;

          justify-content: center;
          align-items: center;

          padding: 25px;

          overflow: hidden;
      }

      #resultado.mostrar {
          display: flex;
      }

      .resultado-conteudo {
          position: relative;
          z-index: 5;

          width: 100%;
          max-width: 650px;

          text-align: center;

          background: white;

          border: 4px solid #1a1a1a;
          border-radius: 40px;

          padding: 40px 30px;

          box-shadow: 0 10px 0 #1a1a1a;
      }

      .resultado-titulo {
          font-size: 52px;
          font-weight: 900;

          color: #ff5252;

          margin-bottom: 10px;
      }

      .mascote {
          width: 150px;
          max-width: 45vw;

          margin: 10px auto;

          object-fit: contain;
      }

      .estrelas {
          font-size: 45px;

          margin: 10px 0;
      }

      .resultado-nota {
          font-size: 30px;
          font-weight: 900;

          margin: 10px 0;
      }

      .resultado-mensagem {
          font-size: 22px;
          font-weight: bold;

          line-height: 1.4;

          margin: 15px auto;

          max-width: 500px;
      }

      .btn-final {
          display: inline-flex;

          min-height: 55px;

          padding: 0 35px;

          border-radius: 30px;

          border: 3px solid #1a1a1a;

          background: #fde05f;

          box-shadow: 0 6px 0 #1a1a1a;

          text-decoration: none;

          align-items: center;
          justify-content: center;

          color: #1a1a1a;

          font-family: inherit;

          font-size: 22px;
          font-weight: 900;

          margin-top: 15px;

          cursor: pointer;
      }

      .btn-final:active {
          transform: translateY(4px);
          box-shadow: 0 2px 0 #1a1a1a;
      }

      .confete {
          position: absolute;

          top: -30px;

          width: 12px;
          height: 20px;

          animation: cair linear forwards;

          z-index: 1;
      }

      @keyframes cair {

          from {
              transform:
                  translateY(-30px) rotate(0deg);
          }

          to {
              transform:
                  translateY(110vh) rotate(720deg);
          }
      }

      @media (max-width: 650px) {

          .pagina {
              padding: 25px 12px;
          }

          .titulo {
              font-size: 31px;
          }

          .subtitulo {
              font-size: 18px;
          }

          .card-questao {
              padding: 22px 15px;
              border-radius: 28px;
          }

          .instrucao {
              font-size: 22px;
          }

          .letra-grande {
              width: 145px;
              height: 145px;

              font-size: 65px;
          }

          .imagem-questao {
              width: 125px;
              height: 125px;
          }

          .palavra-grande {
              font-size: 34px;
          }

          .alternativa {
              min-height: 62px;
              font-size: 25px;
          }

          .resultado-titulo {
              font-size: 39px;
          }

          .resultado-nota {
              font-size: 25px;
          }
      }

      @media (max-width: 420px) {

          .alternativas {
              grid-template-columns: 1fr 1fr;
              gap: 10px;
          }

          .alternativa {
              font-size: 22px;
              min-height: 58px;
          }

          .btn-voltar {
              width: 42px;
              height: 42px;
              top: 12px;
              left: 12px;
          }
      }
    </style>
  </head>

  <body>
    <div class="fundo">
      <div class="bolha bolha1"></div>
      <div class="bolha bolha2"></div>
      <div class="bolha bolha3"></div>
      <div class="bolha bolha4"></div>
    </div>

    <a href="nivel1.php" class="btn-voltar" title="Voltar"> ← </a>

    <main class="pagina">
      <section class="prova">
        <header class="cabecalho">
          <h1 class="titulo">🏆 Desafio do Alfabeto</h1>

          <p class="subtitulo">Você aprendeu todas as letras! Vamos ver o que você lembra? 🌟</p>
        </header>

        <div class="progresso-area">
          <div class="progresso-info">
            <span id="numeroQuestao"> Questão 1 de 20 </span>

            <span id="pontuacao"> ⭐ 0 </span>
          </div>

          <div class="barra">
            <div class="barra-progresso" id="barraProgresso"></div>
          </div>
        </div>

        <section class="card-questao" id="cardQuestao">
          <div class="numero-questao" id="tipoQuestao"></div>

          <div class="instrucao" id="instrucao"></div>

          <div id="areaQuestao"></div>

          <div class="feedback" id="feedback"></div>

          <button type="button" class="btn-proxima" id="btnProxima" disabled>Próxima ➜</button>
        </section>
      </section>
    </main>

    <?php

    $letras = range('A', 'Z');

    foreach ($letras as $letra):

        ?>

    <audio id="somLetra<?= $letra ?>" src="../audios/Letra <?= $letra ?>.m4a" preload="auto"></audio>

    <?php endforeach; ?>

    <audio id="somParabens" src="../audios/parabens.mp3" preload="auto"></audio>

    <div id="resultado">
      <div id="containerConfetes"></div>

      <div class="resultado-conteudo">
        <h2 class="resultado-titulo" id="resultadoTitulo">PARABÉNS!</h2>

        <img src="../img/Luminho.png" alt="Lumi comemorando" class="mascote" onerror="this.src='../img/Luminho.png'" />

        <div class="estrelas" id="resultadoEstrelas">⭐⭐⭐⭐⭐</div>

        <div class="resultado-nota" id="resultadoNota">Você acertou 20 de 20!</div>

        <p class="resultado-mensagem" id="resultadoMensagem"></p>

        <a href="nivel1.php" class="btn-final"> Voltar para a Trilha 🏠 </a>
      </div>
    </div>

    <script>
          const TOTAL_QUESTOES = 20;

          let questaoAtual = 0;
          let pontuacao = 0;
          let respondeu = false;

          const numeroQuestao =
              document.getElementById('numeroQuestao');

          const pontuacaoElemento =
              document.getElementById('pontuacao');

          const barraProgresso =
              document.getElementById('barraProgresso');

          const tipoQuestao =
              document.getElementById('tipoQuestao');

          const instrucao =
              document.getElementById('instrucao');

          const areaQuestao =
              document.getElementById('areaQuestao');

          const feedback =
              document.getElementById('feedback');

          const btnProxima =
              document.getElementById('btnProxima');

          const resultado =
              document.getElementById('resultado');

          const bancoQuestoes = [
              {
                  tipo: 'audio',

                  titulo: '👂 Ouça com atenção!',

                  instrucao:
                      'Qual é a letra que você ouviu?',

                  letra: 'A',

                  alternativas: ['A', 'B', 'D', 'P']
              },

              {
                  tipo: 'audio',

                  titulo: '👂 Ouça com atenção!',

                  instrucao:
                      'Qual é a letra que você ouviu?',

                  letra: 'M',

                  alternativas: ['N', 'M', 'W', 'A']
              },

              {
                  tipo: 'audio',

                  titulo: '👂 Ouça com atenção!',

                  instrucao:
                      'Qual é a letra que você ouviu?',

                  letra: 'S',

                  alternativas: ['C', 'S', 'Z', 'F']
              },

              {
                  tipo: 'audio',

                  titulo: '👂 Ouça com atenção!',

                  instrucao:
                      'Qual é a letra que você ouviu?',

                  letra: 'R',

                  alternativas: ['P', 'R', 'B', 'D']
              },

              {
                  tipo: 'audio',

                  titulo: '👂 Ouça com atenção!',

                  instrucao:
                      'Qual é a letra que você ouviu?',

                  letra: 'T',

                  alternativas: ['D', 'P', 'T', 'F']
              },

              {
                  tipo: 'imagem',

                  titulo: '🖼️ Olhe a figura!',

                  instrucao:
                      'Qual é a primeira letra desta palavra?',

                  palavra: 'ABELHA',

                  imagem: '../img/abelha.png',

                  letra: 'A',

                  alternativas: ['A', 'E', 'B', 'M']
              },

              {
                  tipo: 'imagem',

                  titulo: '🖼️ Olhe a figura!',

                  instrucao:
                      'Qual é a primeira letra desta palavra?',

                  palavra: 'BOLA',

                  imagem: '../img/bola.png',

                  letra: 'B',

                  alternativas: ['D', 'P', 'B', 'A']
              },

              {
                  tipo: 'imagem',

                  titulo: '🖼️ Olhe a figura!',

                  instrucao:
                      'Qual é a primeira letra desta palavra?',

                  palavra: 'COELHO',

                  imagem: '../img/coelho.png',

                  letra: 'C',

                  alternativas: ['G', 'C', 'Q', 'S']
              },

              {
                  tipo: 'imagem',

                  titulo: '🖼️ Olhe a figura!',

                  instrucao:
                      'Qual é a primeira letra desta palavra?',

                  palavra: 'MACACO',

                  imagem: '../img/Macaco.png',

                  letra: 'M',

                  alternativas: ['N', 'M', 'W', 'B']
              },

              {
                  tipo: 'imagem',

                  titulo: '🖼️ Olhe a figura!',

                  instrucao:
                      'Qual é a primeira letra desta palavra?',

                  palavra: 'OVELHA',

                  imagem: '../img/Ovelha.png',

                  letra: 'S',

                  alternativas: ['C', 'S', 'Z', 'P']
              },

              {
                  tipo: 'completar',

                  titulo: '🧩 Complete a palavra!',

                  instrucao:
                      'Qual letra está faltando?',

                  palavra: '_ANANA',

                  resto: 'ANANA',

                  letra: 'B',

                  alternativas: ['A', 'B', 'M', 'P'],

                  imagem: '../img/banana.png'
              },

              {
                  tipo: 'completar',

                  titulo: '🧩 Complete a palavra!',

                  instrucao:
                      'Qual letra está faltando?',

                  palavra: '_BACATE',

                  resto: 'BACATE',

                  letra: 'A',

                  alternativas: ['A', 'B', 'M', 'P'],

                  imagem: '../img/abacate.png'
              },

              {
                  tipo: 'completar',

                  titulo: '🧩 Complete a palavra!',

                  instrucao:
                      'Qual letra está faltando?',

                  palavra: '_ALEIA',

                  resto: 'ALEIA',

                  letra: 'B',

                  alternativas: ['B', 'D', 'P', 'V'],

                  imagem: '../img/baleia.png'
              },

              {
                  tipo: 'completar',

                  titulo: '🧩 Complete a palavra!',

                  instrucao:
                      'Qual letra está faltando?',

                  palavra: '_OLA',

                  resto: 'OLA',

                  letra: 'B',

                  alternativas: ['A', 'B', 'D', 'P'],

                  imagem: '../img/bola.png'
              },

              {
                  tipo: 'completar',

                  titulo: '🧩 Complete a palavra!',

                  instrucao:
                      'Qual letra está faltando?',

                  palavra: '_ATO',

                  resto: 'ATO',

                  letra: 'G',

                  alternativas: ['C', 'G', 'P', 'R'],

                  imagem: '../img/Gato.png'
              },

              {
                  tipo: 'ordem',

                  titulo: '🔤 Pense no alfabeto!',

                  instrucao:
                      'Qual letra vem depois do A?',

                  letra: 'B',

                  alternativas: ['B', 'C', 'D', 'E']
              },

              {
                  tipo: 'ordem',

                  titulo: '🔤 Pense no alfabeto!',

                  instrucao:
                      'Qual letra vem depois do F?',

                  letra: 'G',

                  alternativas: ['E', 'F', 'G', 'H']
              },

              {
                  tipo: 'ordem',

                  titulo: '🔤 Pense no alfabeto!',

                  instrucao:
                      'Qual letra vem antes do M?',

                  letra: 'L',

                  alternativas: ['J', 'K', 'L', 'N']
              },

              {
                  tipo: 'ordem',

                  titulo: '🔤 Pense no alfabeto!',

                  instrucao:
                      'Qual letra vem depois do R?',

                  letra: 'S',

                  alternativas: ['P', 'Q', 'S', 'T']
              },

              {
                  tipo: 'ordem',

                  titulo: '🔤 Pense no alfabeto!',

                  instrucao:
                      'Qual letra vem antes do Z?',

                  letra: 'Y',

                  alternativas: ['W', 'X', 'Y', 'Z']
              }

          ];

          function embaralhar(array) {

              const copia = [...array];

              for (let i = copia.length - 1; i > 0; i--) {

                  const j =
                      Math.floor(Math.random() * (i + 1));

                  [copia[i], copia[j]] =
                      [copia[j], copia[i]];
              }

              return copia;
          }

          let prova = embaralhar(bancoQuestoes)
              .slice(0, TOTAL_QUESTOES)
              .map(questao => {

                  return {
                      ...questao,
                      alternativas:
                          embaralhar(questao.alternativas)
                  };

              });

          function mostrarQuestao() {

              respondeu = false;

              feedback.textContent = '';
              feedback.className = 'feedback';

              btnProxima.disabled = true;

              const q = prova[questaoAtual];

              numeroQuestao.textContent =
                  `Questão ${questaoAtual + 1} de ${TOTAL_QUESTOES}`;

              pontuacaoElemento.textContent =
                  `⭐ ${pontuacao}`;

              const percentual =
                  ((questaoAtual) / TOTAL_QUESTOES) * 100;

              barraProgresso.style.width =
                  percentual + '%';

              tipoQuestao.textContent =
                  q.titulo;

              instrucao.textContent =
                  q.instrucao;

              areaQuestao.innerHTML = '';

              if (q.tipo === 'audio') {

                  criarQuestaoAudio(q);

              }

              else if (q.tipo === 'imagem') {

                  criarQuestaoImagem(q);

              }

              else if (q.tipo === 'completar') {

                  criarQuestaoCompletar(q);

              }

              else if (q.tipo === 'ordem') {

                  criarQuestaoOrdem(q);

              }
          }

          function criarQuestaoAudio(q) {

              const botaoAudio =
                  document.createElement('button');

              botaoAudio.type = 'button';

              botaoAudio.className = 'btn-audio';

              botaoAudio.title =
                  'Ouvir novamente';

              botaoAudio.innerHTML = `
          <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="#1a1a1a"
              stroke-width="2">

              <polygon
                  points="11 5 6 9 2 9 2 15 6 15 11 19 11 5">
              </polygon>

              <path
                  d="M19.07 4.93a10 10 0 0 1 0 14.14">
              </path>

              <path
                  d="M15.54 8.46a5 5 0 0 1 0 7.07">
              </path>

          </svg>
      `;

              botaoAudio.addEventListener(
                  'click',
                  () => tocarLetra(q.letra)
              );

              areaQuestao.appendChild(botaoAudio);

              const texto =
                  document.createElement('div');

              texto.style.fontSize = '20px';
              texto.style.fontWeight = 'bold';
              texto.style.marginBottom = '15px';

              texto.textContent =
                  'Clique no alto-falante para ouvir novamente';

              areaQuestao.appendChild(texto);

              criarAlternativas(q);
          }

          function criarQuestaoImagem(q) {

              const img =
                  document.createElement('img');

              img.className =
                  'imagem-questao';

              img.src =
                  q.imagem;

              img.alt =
                  q.palavra;

              areaQuestao.appendChild(img);

              const palavra =
                  document.createElement('div');

              palavra.className =
                  'palavra-grande';

              palavra.textContent =
                  q.palavra;

              areaQuestao.appendChild(palavra);

              criarAlternativas(q);
          }

          function criarQuestaoCompletar(q) {

              const img =
                  document.createElement('img');

              img.className =
                  'imagem-questao';

              img.src =
                  q.imagem;

              img.alt =
                  'Imagem da palavra';

              areaQuestao.appendChild(img);


              const palavra =
                  document.createElement('div');

              palavra.className =
                  'palavra-grande';

              const primeiraParte =
                  document.createElement('span');

              primeiraParte.className =
                  'lacuna';

              primeiraParte.textContent =
                  '_';

              palavra.appendChild(primeiraParte);

              palavra.appendChild(
                  document.createTextNode(q.resto)
              );

              areaQuestao.appendChild(palavra);

              criarAlternativas(q);
          }

          function criarQuestaoOrdem(q) {

              const simbolo =
                  document.createElement('div');

              simbolo.className =
                  'letra-grande';

              simbolo.textContent =
                  '?';

              areaQuestao.appendChild(simbolo);

              criarAlternativas(q);
          }

          function criarAlternativas(q) {

              const container =
                  document.createElement('div');

              container.className =
                  'alternativas';

              q.alternativas.forEach(alternativa => {

                  const botao =
                      document.createElement('button');

                  botao.type = 'button';

                  botao.className =
                      'alternativa';

                  botao.textContent =
                      alternativa;

                  botao.addEventListener(
                      'click',
                      () => responder(alternativa, q, botao)
                  );

                  container.appendChild(botao);
              });

              areaQuestao.appendChild(container);
          }

          function responder(resposta, q, botaoClicado) {

              if (respondeu) {
                  return;
              }

              respondeu = true;

              const botoes =
                  areaQuestao.querySelectorAll('.alternativa');

              botoes.forEach(botao => {
                  botao.disabled = true;
              });


              const correta =
                  resposta === q.letra;


              if (correta) {

                  pontuacao++;

                  botaoClicado.classList.add('correta');

                  feedback.textContent =
                      escolherMensagemAcerto();

                  feedback.classList.add('acerto');

                  tocarSom('somAcerto');

              }

              else {

                  botaoClicado.classList.add('errada');

                  botoes.forEach(botao => {

                      if (botao.textContent === q.letra) {
                          botao.classList.add('correta');
                      }

                  });

                  feedback.textContent =
                      escolherMensagemErro();

                  feedback.classList.add('erro');

                  tocarSom('somErro');
              }


              pontuacaoElemento.textContent =
                  `⭐ ${pontuacao}`;

              btnProxima.disabled = false;

          }

          function escolherMensagemAcerto() {

              const mensagens = [

                  '🎉 Muito bem!',

                  '⭐ Isso! Você acertou!',

                  '👏 Parabéns!',

                  '🌟 Muito bom!',

                  '🥳 Acertou!'

              ];

              return mensagens[
                  Math.floor(
                      Math.random() * mensagens.length
                  )
              ];
          }


          function escolherMensagemErro() {

              const mensagens = [

                  '💛 Tudo bem! Vamos continuar!',

                  '🌱 Vamos para a próxima!',

                  '😊 Não tem problema!',

                  '💪 Continue tentando!'

              ];

              return mensagens[
                  Math.floor(
                      Math.random() * mensagens.length
                  )
              ];
          }

          btnProxima.addEventListener(
              'click',
              () => {

                  if (!respondeu) {
                      return;
                  }

                  questaoAtual++;

                  if (questaoAtual >= TOTAL_QUESTOES) {

                      finalizarProva();

                  }

                  else {

                      mostrarQuestao();

                  }

              }
          );

          function tocarSom(idAudio) {

              const audio =
                  document.getElementById(idAudio);

              if (!audio) {
                  return;
              }

              audio.currentTime = 0;

              audio.play().catch(() => { });
          }


          function tocarLetra(letra) {

              tocarSom(
                  'somLetra' + letra.toUpperCase()
              );
          }

          function finalizarProva() {

              barraProgresso.style.width = '100%';

              salvarResultado();

              setTimeout(() => {

                  mostrarResultado();

              }, 500);
          }

          function salvarResultado() {

              const dados =
                  new URLSearchParams();

              dados.append(
                  'salvar_prova',
                  '1'
              );

              dados.append(
                  'nota',
                  pontuacao
              );

              dados.append(
                  'total',
                  TOTAL_QUESTOES
              );


              fetch('prova.php', {

                  method: 'POST',

                  headers: {
                      'Content-Type':
                          'application/x-www-form-urlencoded'
                  },

                  body: dados.toString()

              })
                  .catch(() => {
                  });
          }

          function mostrarResultado() {

              resultado.classList.add('mostrar');

              const estrelas =
                  calcularEstrelas(pontuacao);

              const resultadoEstrelas =
                  document.getElementById(
                      'resultadoEstrelas'
                  );

              resultadoEstrelas.textContent =
                  estrelas;


              document.getElementById(
                  'resultadoNota'
              ).textContent =
                  `Você acertou ${pontuacao} de ${TOTAL_QUESTOES}!`;


              const resultadoTitulo =
                  document.getElementById(
                      'resultadoTitulo'
                  );

              const resultadoMensagem =
                  document.getElementById(
                      'resultadoMensagem'
                  );


              if (pontuacao >= 18) {

                  resultadoTitulo.textContent =
                      '🏆 MESTRE DO ALFABETO!';

                  resultadoMensagem.textContent =
                      'Você conhece muito bem as letras! O Lumi está muito orgulhoso de você! 🌟';

                  tocarSom('somParabens');

              }

              else if (pontuacao >= 14) {

                  resultadoTitulo.textContent =
                      '🌈 AVENTUREIRO DO ALFABETO!';

                  resultadoMensagem.textContent =
                      'Muito bem! Você já aprendeu muitas letras. Continue praticando e você ficará ainda melhor! ⭐';

                  tocarSom('somParabens');

              }

              else {

                  resultadoTitulo.textContent =
                      '🌱 PEQUENO EXPLORADOR!';

                  resultadoMensagem.textContent =
                      'Você está aprendendo! Continue praticando com o Lumi. Cada tentativa ajuda você a aprender mais! 💛';

              }


              gerarConfetes();
          }

          function calcularEstrelas(nota) {

              if (nota >= 18) {
                  return '⭐⭐⭐⭐⭐';
              }

              if (nota >= 14) {
                  return '⭐⭐⭐⭐';
              }

              if (nota >= 10) {
                  return '⭐⭐⭐';
              }

              if (nota >= 5) {
                  return '⭐⭐';
              }

              return '⭐';
          }

          function gerarConfetes() {

              const container =
                  document.getElementById(
                      'containerConfetes'
                  );

              if (!container) {
                  return;
              }

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


              for (let i = 0; i < 80; i++) {

                  const confete =
                      document.createElement('div');

                  confete.className =
                      'confete';

                  confete.style.backgroundColor =
                      cores[
                      Math.floor(
                          Math.random() * cores.length
                      )
                      ];

                  confete.style.left =
                      Math.random() * 100 + '%';

                  confete.style.animationDuration =
                      (2.5 + Math.random() * 3) + 's';

                  confete.style.animationDelay =
                      Math.random() * 1.5 + 's';

                  container.appendChild(confete);
              }
          }

          mostrarQuestao();
    </script>
  </body>
</html>