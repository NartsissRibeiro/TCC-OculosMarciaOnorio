<?php
include '../../db/conexao.php';
include '../session/session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();

    if (isset($_POST['id_produto']) && isset($_POST['quantidade'])) {
        $idproduto = (int)$_POST['id_produto'];
        $quantidade = (int)$_POST['quantidade'];

        $sqlCheck = "SELECT qtd_carrinho FROM carrinho WHERE id_user = ? AND id_produto = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $iduser, $idproduto);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novaQtd = $row['qtd_carrinho'] + $quantidade;

            $sqlUpdate = "UPDATE carrinho SET qtd_carrinho = ? WHERE id_user = ? AND id_produto = ?";
            $stmtUpdate = $conexao->prepare($sqlUpdate);
            $stmtUpdate->bind_param("iii", $novaQtd, $iduser, $idproduto);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $sqlInsert = "INSERT INTO carrinho (id_user, id_produto, qtd_carrinho) VALUES (?, ?, ?)";
            $stmtInsert = $conexao->prepare($sqlInsert);
            $stmtInsert->bind_param("iii", $iduser, $idproduto, $quantidade);
            $stmtInsert->execute();
            $stmtInsert->close();
        }

        $stmtCheck->close();
        header('Location: ../../views/carrinho/index.php');
        exit();
    } else {
        echo "<div class='alert alert-warning'>Dados de produto ou quantidade não recebidos.</div>";
        var_dump($_POST);
    }
} else {
    $message = "Você precisa estar logado para adicionar itens ao carrinho.";
    header('Location: ../../views/telainicial/index.php?message=' . urlencode($message) . '#menu');
    exit();
}

$conexao->close();
?>
