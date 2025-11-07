<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Parabéns! Curso Concluído</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        a.btn {
            background-color: #fff;
            color: #388e3c;
            font-weight: bold;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
        }

        a.btn:hover {
            background-color: #c8e6c9;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>🏆 Parabéns!</h1>
        <p>Você concluiu com sucesso todos os níveis do curso.</p>
        <p>Continue praticando e compartilhando seu conhecimento!</p>
        <a href="login.php" class="btn">Voltar ao Login</a>
    </div>
</body>

</html>