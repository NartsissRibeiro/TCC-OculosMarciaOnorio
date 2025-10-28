<?php
include "../../db/conexao.php";
include '../Session/Session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    $id_cor = $_GET['id_cor'] ?? null;

    if ($id_cor) {
        $sql = "DELETE FROM cor WHERE id_cor = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id_cor);

        if ($stmt->execute()) {
            header("Location: ../../views/cor/index.php?msg=Item excluído com sucesso.");
        } else {
            header("Location: ../../views/cor/index.php?msg=Erro ao excluir item.");
        }
        $stmt->close();
    } else {
        header("Location: ../../views/cor/index.php?msg=ID inválido.");
    }
} else {
    header("Location: ../../views/cor/index.php");
    exit;
}

$conexao->close();
