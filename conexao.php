<?php

$servername = "localhost";

$database = "decords";
$username = "root";
$password = "";
//Create connection

$conn = new mysqli($servername, $database, $username, $password);

// Check connection

// Verificar conexão
/*
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);  // Exibe o erro e encerra o script
}
echo "Connected successfully";  // Só exibe essa mensagem se a conexão for bem-sucedida
