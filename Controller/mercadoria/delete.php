<?php
include "../../db/conexao.php";
include '../Session/Session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    $id_mercadoria = $_GET['id_mercadoria'] ?? null;

    if ($id_mercadoria) {
        $sql = "DELETE FROM mercadoria WHERE id_mercadoria = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id_mercadoria);

        if ($stmt->execute()) {
            header("Location: ../../views/mercadoria/index.php?msg=Item excluído com sucesso.");
        } else {
            header("Location: ../../views/mercadoria/index.php?msg=Erro ao excluir item.");
        }
        $stmt->close();
    } else {
        header("Location: ../../views/mercadoria/index.php?msg=ID inválido.");
    }
} else {
    header("Location: ../../views/mercadoria/index.php");
    exit;
}

$conexao->close();
