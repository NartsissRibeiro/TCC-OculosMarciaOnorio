<?php 
session_start();
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";

if (!SessionController::isLoggedIn()) {
    die("<div class='alert alert-danger text-center mt-5'>Você precisa estar logado.</div>");
}

$userId = SessionController::getUserId();
$pedidoId = $_GET['id_pedido'] ?? null;

if (!$pedidoId) {
    die("<div class='alert alert-danger text-center mt-5'>Pedido inválido.</div>");
}

$stmt = $conexao->prepare("SELECT id_pedido, valor_total, id_status, tipo_pagamento FROM pedido WHERE id_pedido=? AND id_user=?");
$stmt->bind_param("ii", $pedidoId, $userId);
$stmt->execute();
$stmt->bind_result($id, $valor_total, $id_status, $tipo_pagamento_existente);
$stmt->fetch();
$stmt->close();

if (!$id) {
    die("<div class='alert alert-danger text-center mt-5'>Pedido não encontrado.</div>");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor']));

    if ($tipo_pagamento_existente) {
        $msg = "<div class='alert alert-info'>Pedido já foi pago com $tipo_pagamento_existente.</div>";
    } else {
        $stmt = $conexao->prepare("
            UPDATE pedido 
            SET tipo_pagamento = ?, id_status = (SELECT id_status FROM status WHERE tipo_status='pago')
            WHERE id_pedido = ?
        ");
        $stmt->bind_param("si", $tipo_pagamento, $pedidoId);

        if ($stmt->execute()) {
            $stmtItems = $conexao->prepare("SELECT id_produto, quantidade FROM pedido_item WHERE id_pedido = ?");
            $stmtItems->bind_param("i", $pedidoId);
            $stmtItems->execute();
            $resultItems = $stmtItems->get_result();

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

            $msg = "<div class='alert alert-success'>Pagamento registrado e estoque atualizado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao processar pagamento.</div>";
        }

        $stmt->close();
    }
}

include "../partials/header.php";
include "../partials/navbar.php";
?>

<div class="container mt-5">
    <div class="card shadow" style="max-width:600px; margin:auto; padding:20px;">
        <h3 class="text-center mb-4">Opções de pagamento<?php //echo $pedidoId; ?></h3>

        <?php if ($msg) echo $msg; ?>

        <?php if (!$tipo_pagamento_existente): ?>
        <form method="POST">
            <div class="mb-3">
                <label for="tipo_pagamento" class="form-label">Método de Pagamento</label>
                <select name="tipo_pagamento" id="tipo_pagamento" class="form-select" required>
                    <option value="">Selecione...</option>
                    <option value="cartao">Cartão de Crédito</option>
                    <option value="boleto">Boleto</option>
                    <option value="pix">PIX</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor a Pagar</label>
                <input type="text" name="valor" id="valor" value="<?php echo number_format($valor_total, 2, ',', '.'); ?>" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary w-100">Pagar</button>
        </form>
        <?php endif; ?>

    </div>
</div>

<?php include "../partials/footer.php"; ?>
