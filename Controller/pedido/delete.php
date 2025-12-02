<?php
require_once "../../db/conexao.php";
require_once "../session/session.php";

if (!SessionController::isLoggedIn()) {
    header("Location: ../../view/usuario/index.php");
    exit;
}

$userId = SessionController::getUserId();
$pedidoId = $_GET['idpedido'] ?? null;

if (!$pedidoId) {
    echo "ID do pedido não informado.";
    exit;
}

$stmt = $conexao->prepare("SELECT id_pedido FROM pedido WHERE id_pedido = ? AND id_user = ?");
$stmt->bind_param("ii", $pedidoId, $userId);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-danger'>Pedido não encontrado ou não pertence a você.</div>";
    exit;
}

$stmtDelItens = $conexao->prepare("DELETE FROM pedido_item WHERE id_pedido = ?");
$stmtDelItens->bind_param("i", $pedidoId);
$stmtDelItens->execute();

$stmtDelPedido = $conexao->prepare("DELETE FROM pedido WHERE id_pedido = ? AND id_user = ?");
$stmtDelPedido->bind_param("ii", $pedidoId, $userId);

if ($stmtDelPedido->execute()) {
    header("Location: ../../views/usuario/pedido.php?msg=Pedido+deletado+com+sucesso");
    exit;
} else {
    echo "<div class='alert alert-danger'>Erro ao deletar o pedido.</div>";
}
