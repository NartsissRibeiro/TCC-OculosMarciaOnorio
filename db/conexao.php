<?php
$nomeserver = "localhost";
$usarname = "root";
$senha = "";
$nomedb = "marciaonoriodb";

// Cria a conexão
$conexao = mysqli_connect($nomeserver, $usarname, $senha, $nomedb);

// Verifica a conexão
if (!$conexao) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}
?>
