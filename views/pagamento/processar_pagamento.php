<?php
session_start();
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";

if (!SessionController::isLoggedIn()) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor'])); // converte para decimal

    // --- Atualiza o pedido para 'pago' ---
    $stmt = $conexao->prepare("
        UPDATE pedido 
        SET tipo_pagamento = ?, id_status = (SELECT id_status FROM status WHERE tipo_status='pago')
        WHERE id_pedido = ?
    ");
    $stmt->bind_param("si", $tipo_pagamento, $id_pedido);

    if ($stmt->execute()) {

        // --- Busca os produtos do pedido ---
        $stmtItems = $conexao->prepare("
            SELECT id_produto, quantidade 
            FROM pedido_item 
            WHERE id_pedido = ?
        ");
        $stmtItems->bind_param("i", $id_pedido);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();

        // --- Diminui o estoque de cada produto ---
        while ($item = $resultItems->fetch_assoc()) {
            $stmtUpdate = $conexao->prepare("
                UPDATE produto 
                SET estoque_produto = estoque_produto - ? 
                WHERE id_produto = ? AND estoque_produto >= ?
            ");
            $stmtUpdate->bind_param("iii", $item['quantidade'], $item['id_produto'], $item['quantidade']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        $stmtItems->close();

        echo "<div class='alert alert-success text-center mt-5'>
                Pagamento registrado e estoque atualizado com sucesso!
              </div>";
        echo "<div class='text-center mt-3'>
                <a href='../telainicial/index.php' class='btn btn-primary'>Voltar ao Início</a>
              </div>";

    } else {
        echo "<div class='alert alert-danger text-center mt-5'>
                Erro ao processar pagamento.
              </div>";
    }

    $stmt->close();
}

$conexao->close();
?>
