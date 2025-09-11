<?php
include "../../db/conexao.php";
include '../Session/Session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    $id_produto = $_GET['id_produto'] ?? null;

    if ($id_produto) {
        $sql = "DELETE FROM produto WHERE id_produto = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id_produto);

        if ($stmt->execute()) {
            header("Location: ../../views/produto/index.php?msg=Item excluído com sucesso.");
        } else {
            header("Location: ../../views/produto/index.php?msg=Erro ao excluir item.");
        }
        $stmt->close();
    } else {
        header("Location: ../../views/produto/index.php?msg=ID inválido.");
    }
} else {
    header("Location: ../../views/produto/index.php");
    exit;
}

$conexao->close();
