<?php
include "../../db/conexao.php";
include '../Session/Session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    $id_material = $_GET['id_material'] ?? null;

    if ($id_material) {
        $sql = "DELETE FROM material WHERE id_material = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id_material);

        if ($stmt->execute()) {
            header("Location: ../../views/material/index.php?msg=Item excluído com sucesso.");
        } else {
            header("Location: ../../views/material/index.php?msg=Erro ao excluir item.");
        }
        $stmt->close();
    } else {
        header("Location: ../../views/material/index.php?msg=ID inválido.");
    }
} else {
    header("Location: ../../views/material/index.php");
    exit;
}

$conexao->close();
