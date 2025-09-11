<?php 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

$idcor = $_POST['id_material'];
$name_material = $_POST['name_material'];

$comando_sql = "INSERT INTO material (nome_material) 
                VALUES ('$name_material')";

if (mysqli_query($conexao, $comando_sql)) {
    header('Location: ../../views/material/new.php');
} else {
    echo "Error: " . $comando_sql . "<br>" . mysqli_error($conexao);
}
?>