<?php 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

$idcor = $_POST['id_cor'];
$name_cor = $_POST['name_cor'];

$comando_sql = "INSERT INTO cor (nome_cor) 
                VALUES ('$name_cor',')";

if (mysqli_query($conexao, $comando_sql)) {
    header('Location: ../../views/cor/new.php');
} else {
    echo "Error: " . $comando_sql . "<br>" . mysqli_error($conexao);
}
?>