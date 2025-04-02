<?php

$servername = "localhost";
$dbname = "decords_bd";
$username = "root"; // Nome de usuário do MySQL
$password = ""; // Senha do MySQL (vazia no XAMPP por padrão)

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error); // Exibe o erro e encerra o script
}

// Configurar o charset para UTF-8
$conn->set_charset("utf8");
