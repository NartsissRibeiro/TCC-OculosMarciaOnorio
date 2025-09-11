<?php
session_start(); 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

$id_fornecedor = $_POST['id_fornecedor'] ?? null;
$name_fornecedor = $_POST['name_fornecedor'] ?? null;

$comando_sql = "INSERT INTO fornecedor (nome_fornecedor) 
                VALUES ('$name_fornecedor')";

if (mysqli_query($conexao, $comando_sql)) {
    $_SESSION['msg'] = "Fornecedor registrado com sucesso!";
    $_SESSION['msg_type'] = "success";
    header('Location: ../../views/fornecedor/new.php');
    exit;
} else {
    $_SESSION['msg'] = "Erro ao registrar fornecedor: " . mysqli_error($conexao);
    $_SESSION['msg_type'] = "danger";
    header('Location: ../../views/fornecedor/new.php');
    exit;
}
?>
