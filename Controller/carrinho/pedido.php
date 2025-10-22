<?php
session_start();
include_once "../../Controller/Session/Session.php";
include "../../db/conexao.php";
require_once "../../Controller/MailController.php";

// Verifica login
if (!SessionController::isLoggedIn()) {
    die("<div class='alert alert-danger'>Você precisa estar logado para finalizar o pedido.</div>");
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

    // --- 5. Status ---
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

    // --- Monta corpo do email ---
    $body = "<h2>Pedido Confirmado #$pedidoId</h2>";
    $body .= "<p>Olá! Seu pedido foi registrado com sucesso.</p>";
    $body .= "<ul>";
    foreach ($produtos as $id_produto => $quantidade) {
        $stmtProd = $conexao->prepare("SELECT nome_produto, preco_produto FROM produto WHERE id_produto = ?");
        $stmtProd->bind_param("i", $id_produto);
        $stmtProd->execute();
        $resProd = $stmtProd->get_result()->fetch_assoc();
        $subtotal = $resProd['preco_produto'] * $quantidade;
        $body .= "<li>{$resProd['nome_produto']} x $quantidade - R$ " . number_format($subtotal,2,',','.') . "</li>";
    }
    $body .= "</ul>";
    $body .= "<p>Total: R$ " . number_format($total,2,',','.') . "</p>";
    $body .= "<p>Endereço: $rua, $numero, $bairroNome, $cidadeNome - $estadoNome</p>";
    if ($mensagem) $body .= "<p>Mensagem: $mensagem</p>";

    // --- Busca email do usuário ---
    $stmtUser = $conexao->prepare("SELECT email_user FROM usuarios WHERE id_user = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $userEmail = $stmtUser->get_result()->fetch_assoc()['email_user'];

    // --- Envia email ---
    MailController::sendMail($userEmail, "Confirmação do Pedido #$pedidoId", $body);

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
?>
