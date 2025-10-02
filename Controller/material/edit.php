<?php
include "../../db/conexao.php";

$id_material = isset($_POST['id_material']) ? (int) $_POST['id_material'] : 0;
$nome_material = isset($_POST['nome_material']) ? $conexao->real_escape_string($_POST['nome_cor']) : '';

if ($id_material > 0 && $nome_material != '') {
    $sql = "UPDATE material SET nome_material='$nome_material' WHERE id_material=$id_material";

    if ($conexao->query($sql) === TRUE) {
        header('Location: ../../views/material/index.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar o material: " . $conexao->error . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Dados inválidos.</div>";
}

$conexao->close();
?>
