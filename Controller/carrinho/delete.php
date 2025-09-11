<?php
include "../../db/conexao.php";
include '../Session/Session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    $id_carrinho = $_GET['id_carrinho'] ?? null;

    if ($id_carrinho) {
        $sql = "DELETE FROM carrinho WHERE id_carrinho = ? AND id_user = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id_carrinho, $iduser);

        if ($stmt->execute()) {
            header("Location: ../../views/carrinho/index.php?msg=Item excluído com sucesso.");
        } else {
            header("Location: ../../views/carrinho/index.php?msg=Erro ao excluir item.");
        }
        $stmt->close();
    } else {
        header("Location: ../../views/carrinho/index.php?msg=ID inválido.");
    }
} else {
    header("Location: ../../views/login.php");
    exit;
}

$conexao->close();
