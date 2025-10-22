<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';  

$name_cor = $_POST['name_cor'];

$comando_sql = "INSERT INTO cor (nome_cor) VALUES ('$name_cor')";

if (mysqli_query($conexao, $comando_sql)) {
    header('Location: ../../views/cor/new.php');
    exit;
} else {
    echo "Erro ao inserir: " . mysqli_error($conexao);
}
