<?php
include '../../db/conexao.php';
include '../Session/Session.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!SessionController::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$userId = SessionController::getUserId();
$id_pedido = intval($_POST['id_pedido'] ?? 0);
$valor = floatval($_POST['valor'] ?? 0.00);
$metodo = $_POST['metodo_pagamento'] ?? 'simulado';

if (!$id_pedido) {
    echo json_encode(['success' => false, 'message' => 'Pedido inválido']);
    exit;
}

// Verifica pedido pertence ao usuário e pega status atual
$stmt = $conexao->prepare("SELECT id_pedido, valor_total, id_status FROM pedido WHERE id_pedido = ? AND id_user = ?");
$stmt->bind_param("ii", $id_pedido, $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
    exit;
}
$pedido = $res->fetch_assoc();

// Simulação: gera transaction id aleatório
$transaction_id = 'SIM-' . strtoupper(bin2hex(random_bytes(4)));

// Define novo status: suponha que id_status = 2 significa 'Pago'.
// Ajuste conforme sua tabela `status`.
$id_status_pago = 2;

$now = date('Y-m-d H:i:s');

// Atualiza pedido: marca como pago e grava método
$update = $conexao->prepare("UPDATE pedido SET id_status = ?, metodo_pagamento = ?, valor_pago = ?, data_pagamento = ? WHERE id_pedido = ? AND id_user = ?");
$valor_pago = $valor; // ou $pedido['valor_total'] para confiar no DB
$update->bind_param("isssii", $id_status_pago, $metodo, $valor_pago, $now, $id_pedido, $userId);

if (!$update->execute()) {
    echo json_encode(['success' => false, 'message' => 'Falha ao atualizar pedido']);
    exit;
}

// Opcional: registra pagamento em tabela separada
$insertPay = $conexao->prepare("INSERT INTO pagamento (id_pedido, id_user, metodo, valor, transaction_id, criado_em) VALUES (?, ?, ?, ?, ?, ?)");
if ($insertPay) {
    $criado_em = $now;
    $insertPay->bind_param("iissss", $id_pedido, $userId, $metodo, $valor_pago, $transaction_id, $criado_em);
    $insertPay->execute();
    $insertPay->close();
}

// Tudo certo: retorna redirect para página do pedido finalizado
$redirectUrl = '/views/pedido/show.php?id_pedido=' . $id_pedido; // ajuste a rota conforme sua app

echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
exit;
?>
