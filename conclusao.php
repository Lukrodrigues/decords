<?php
session_start();
require_once "conexao.php";
session_unset();
session_destroy();

// Verifica login
if (isset($_POST['finalizar'])) {
    header('Location: login.php');
    exit;
}

$alunoId = (int)$_SESSION['aluno_id'];
$nomeUsuario = $_SESSION['aluno_nome'] ?? 'Aluno';

// Função para calcular o desempenho médio final
function calcularMediaFinal($conn, $alunoId)
{
    $totalNiveis = 3;
    $soma = 0;

    for ($nivel = 1; $nivel <= $totalNiveis; $nivel++) {
        $stmt = $conn->prepare("
			SELECT 
				COUNT(ae.id) AS total,
				SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos
			FROM alunos_exercicios ae
			JOIN exercicios e ON e.id = ae.id_exercicio
			WHERE ae.id_usuario = ? AND e.nivel = ?
		");
        $stmt->bind_param("ii", $alunoId, $nivel);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res['total'] > 0) {
            $percentual = ($res['acertos'] / $res['total']) * 100;
            $soma += $percentual;
        }
    }
    return round($soma / $totalNiveis, 2);
}

$mediaFinal = calcularMediaFinal($conn, $alunoId);

// Caso o aluno acesse sem ter completado tudo, redireciona de volta
if ($mediaFinal < 60) {
    header("Location: tutorial-01.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Conclusão do Curso</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #e6f0ff, #f5f9ff);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #003366;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            padding: 40px 60px;
            text-align: center;
            max-width: 600px;
        }

        h1 {
            color: #0056b3;
            font-size: 28px;
            margin-bottom: 10px;
        }

        h2 {
            color: #007bff;
            font-size: 22px;
            margin-top: 5px;
        }

        p {
            font-size: 18px;
            color: #333;
            margin: 15px 0;
        }

        .highlight {
            color: #0056b3;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .button:hover {
            background: #0056b3;
        }

        .trophy {
            font-size: 70px;
            color: #ffcc00;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="trophy">🏆</div>
        <h1>Parabéns, <?php echo htmlspecialchars($nomeUsuario); ?>!</h1>
        <p>Você concluiu todos os níveis do curso com um desempenho médio de</p>
        <h2><?php echo $mediaFinal; ?>%</h2>

        <?php if ($mediaFinal >= 90): ?>
            <p class="highlight">Excelente! Você demonstrou domínio total do conteúdo. 🎶</p>
        <?php elseif ($mediaFinal >= 75): ?>
            <p class="highlight">Ótimo desempenho! Continue praticando para alcançar a perfeição. 🎸</p>
        <?php else: ?>
            <p class="highlight">Bom trabalho! Você superou o desafio com sucesso. 🎵</p>
        <?php endif; ?>

        <a href="login.php" class="button">Sair e voltar ao início</a>
    </div>
</body>
<form method="post">
    <button type="submit" name="finalizar">Finalizar e Sair</button>
</form>

</html>