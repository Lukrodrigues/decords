<?php

$servername = "localhost";
$dbname = "decords_bd";
$password = "";
$username = "root";

//Create connection

$conn = new mysqli($servername, $dbname, $password, $username,);

// Verificar conexão


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error); // Exibe o erro e encerra o script
}
echo "Connected successfullyyyyyy";

/* Só exibe essa mensagem se a conexão for bem-sucedida
header('Content-Type: text/html; charset=utf-8');
$conn = mysqli_connect('localhost', 'decords_bd', 'root', '') or die("Erro na conexao");
mysqli_select_db($conn, "decords",) or die("base n���o encontrada");
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, 'SET character_set_connection=utf8');
mysqli_query($conn, 'SET character_set_client=utf8');
mysqli_query($conn, 'SET character_set_results=utf8');
ini_set('default_charset', 'UTF-8');
*/