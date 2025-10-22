<?php
include "../../db/conexao.php";

// Pegando dados via POST (melhor que GET)
$id_material   = $_POST['id_material'];
$nome_material = $_POST['nome_material'];

// Montando SQL
$sql = "UPDATE material 
        SET nome_material = '$nome_material' 
        WHERE id_material = $id_material";

if ($conexao->query($sql) === TRUE) {
    // Redireciona para a listagem após sucesso
    header('Location: ../../views/material/index.php?msg=Material atualizado com sucesso');
    exit;
} else {
    echo "Erro ao atualizar: " . $conexao->error;
}

$conexao->close();
