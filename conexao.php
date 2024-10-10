<?php
/*header('Content-Type: text/html; charset=utf-8');
$conectar = mysqli_connect("localhost",'root',"") or die("Erro na conexao");
    mysqli_select_db($conectar,"decords") or die ("base não encontrada");
    mysqli_query($conectar,"SET NAMES 'utf8'");
    mysqli_query($conectar,'SET character_set_connection=utf8');
    mysqli_query($conectar,'SET character_set_client=utf8');
    mysqli_query($conectar,'SET character_set_results=utf8');
    ini_set('default_charset','UTF-8');
*/

$servername = "localhost";
$database = "decords";
$username = "root";
$password = "";
//Create connection
$conn = new mysqli($servername, $database, $username, $password);
// Check connection
/*if ($conn) {
  error_log('Connection failed: ' . mysqli_connect_error());
}
echo "Connected successfully";

//mysqli_close($conn);*/
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);  // Exibe o erro e encerra o script
}
echo "Connected successfully";  // Só exibe essa mensagem se a conexão for bem-sucedida
