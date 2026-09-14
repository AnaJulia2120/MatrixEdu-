<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "matrixedu";

$conn = new mysqli($servidor, $usuario, $senha, $banco);
if ($conn->connect_error) {
    http_response_code(500);
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
