<?php
include '../../db/conexao.php';
include '../session/session.php';
session_start();

if (SessionController::isLoggedIn()) {
    $iduser = SessionController::getUserId();
    
    if (isset($_POST['id_produto']) && isset($_POST['quantidade'])) {
        $idproduto = $_POST['id_produto'];
        $quantidade = (int)$_POST['quantidade'];
        
        $sql = "INSERT INTO carrinho(id_user, id_produto, qtd_carrinho) VALUES (?, ?, ?)";
        
        if ($stmt = $conexao->prepare($sql)) {
            $stmt->bind_param("iii", $iduser, $idproduto, $quantidade);
            
            if ($stmt->execute()) {
                header('location: ../../views/carrinho/index.php');
            } else {
                echo "<div class='alert alert-danger'>Erro ao adicionar o produto ao carrinho.</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Erro na preparação da consulta.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Dados de cesta ou quantidade não recebidos.</div>";
        var_dump($_POST);
    }
} else {
    $message = "Você precisa estar logado para adicionar itens ao carrinho.";
   header('Location: ../../views/telainicial/index.php?message=' . urlencode($message) . '#menu');
    exit();
}

$conexao->close();
?>
