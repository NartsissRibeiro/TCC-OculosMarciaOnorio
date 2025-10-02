<?php
session_start();
include_once "../../Controller/Session/Session.php";
include "../../db/conexao.php";

// Verifica login
if (!SessionController::isLoggedIn()) {
    die("Você precisa estar logado para finalizar o pedido.");
}

$userId = SessionController::getUserId();

// Recebe dados do formulário
$total = $_POST['total'] ?? 0;
$produtos = $_POST['produtos'] ?? []; // array[id_produto] = quantidade
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$bairroNome = $_POST['bairro'] ?? '';
$cidadeNome = $_POST['cidade'] ?? '';
$estadoNome = $_POST['estado'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? null;
$mensagem = $_POST['mensagem'] ?? null;
//$tipoPagamento = $_POST['pagamento'] ?? '';

// Inicia transação
$conexao->begin_transaction();

try {
    // --- 1. Estado ---
    $stmt = $conexao->prepare("SELECT id_estado FROM estado WHERE nome_estado = ?");
    $stmt->bind_param("s", $estadoNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $estadoId = $res->fetch_assoc()['id_estado'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO estado (nome_estado) VALUES (?)");
        $stmt->bind_param("s", $estadoNome);
        $stmt->execute();
        $estadoId = $conexao->insert_id;
    }

    // --- 2. Cidade ---
    $stmt = $conexao->prepare("SELECT id_cidade FROM cidade WHERE nome_cidade = ?");
    $stmt->bind_param("s", $cidadeNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $cidadeId = $res->fetch_assoc()['id_cidade'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO cidade (nome_cidade, id_estado) VALUES (?, ?)");
        $stmt->bind_param("si", $cidadeNome, $estadoId);
        $stmt->execute();
        $cidadeId = $conexao->insert_id;
    }

    // --- 3. Bairro ---
    $stmt = $conexao->prepare("SELECT id_bairro FROM bairro WHERE nome_bairro = ?");
    $stmt->bind_param("s", $bairroNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $bairroId = $res->fetch_assoc()['id_bairro'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO bairro (nome_bairro, cep) VALUES (?, ?)");
        $stmt->bind_param("ss", $bairroNome, $cep);
        $stmt->execute();
        $bairroId = $conexao->insert_id;
    }

    // --- 4. Logradouro ---
    $logradouroCompleto = $rua . ', ' . $numero;
    $stmt = $conexao->prepare("INSERT INTO logradouro (logradouro, id_tipo) VALUES (?, 1)");
    $stmt->bind_param("s", $logradouroCompleto);
    $stmt->execute();
    $logradouroId = $conexao->insert_id;

    // --- 5. Pagamento ---
    //$stmt = $conexao->prepare("INSERT INTO pagamento (tipo_pagamento) VALUES (?)");
    //$stmt->bind_param("s", $tipoPagamento);
    //$stmt->execute();
    //$pagamentoId = $conexao->insert_id;

    $acao = $_POST['acao'] ?? 'pago'; // valor vindo do botão
    $statusId = ($acao === 'nao_pago') ? 2 : 1; // 1 = Não Pago, 2 = Pago

    // --- 6. Inserir pedido ---
    $stmt = $conexao->prepare("
        INSERT INTO pedido 
        (data_pedido, id_status, id_bairro, complemento, mensagem, valor_total, id_logradouro, id_user, id_cidade)
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iissdiii",
        $statusId,
        $bairroId,
        $complemento,
        $mensagem,
        $total,
        $logradouroId,
        $userId,
        $cidadeId
    );
    $stmt->execute();
    $pedidoId = $conexao->insert_id;

    // --- 7. Inserir produtos no pedido_item ---
    $stmtItem = $conexao->prepare("INSERT INTO pedido_item (id_pedido, id_produto, quantidade) VALUES (?, ?, ?)");
    foreach ($produtos as $idProduto => $qtd) {
        $stmtItem->bind_param("iii", $pedidoId, $idProduto, $qtd);
        $stmtItem->execute();
    }

    // --- 8. Limpar carrinho ---
    $stmt = $conexao->prepare("DELETE FROM carrinho WHERE id_user = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Commit
    $conexao->commit();

    // Redirecionar para página de sucesso
    header("Location: ../../views/pedido/sucesso.php?id_pedido=$pedidoId");
    exit;

} catch (Exception $e) {
    $conexao->rollback();
    echo "<div class='alert alert-danger'>Erro ao finalizar pedido: " . $e->getMessage() . "</div>";
}
