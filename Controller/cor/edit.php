<?php
include "../../db/conexao.php";

$id_cor = isset($_POST['id_cor']) ? (int) $_POST['id_cor'] : 0;
$nome_cor = isset($_POST['nome_cor']) ? $conexao->real_escape_string($_POST['nome_cor']) : '';

if ($id_cor > 0 && $nome_cor != '') {
    $sql = "UPDATE cor SET nome_cor='$nome_cor' WHERE id_cor=$id_cor";

    if ($conexao->query($sql) === TRUE) {
        header('Location: ../../views/cor/index.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar a cor: " . $conexao->error . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Dados inválidos: " . $conexao->error . "</div>";
}

$conexao->close();
?>
