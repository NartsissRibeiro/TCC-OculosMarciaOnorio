<?php 
include_once "../../Controller/Session/Session.php"; 
include "../../db/conexao.php"; 

if (!SessionController::isLoggedIn()) {
    die("Você precisa estar logado para ver esta página.");
}

$userId = SessionController::getUserId();
$pedidoId = $_GET['id_pedido'] ?? null;

if (!$pedidoId) {
    die("Pedido inválido.");
}

$stmt = $conexao->prepare("
    SELECT p.id_pedido, p.data_pedido, p.valor_total, p.complemento, p.mensagem,
           st.tipo_status, l.logradouro, b.nome_bairro, c.nome_cidade, e.nome_estado
    FROM pedido p
    INNER JOIN status st ON p.id_status = st.id_status
    INNER JOIN logradouro l ON p.id_logradouro = l.id_logradouro
    INNER JOIN bairro b ON p.id_bairro = b.id_bairro
    INNER JOIN cidade c ON p.id_cidade = c.id_cidade
    INNER JOIN estado e ON c.id_estado = e.id_estado
    WHERE p.id_pedido = ? AND p.id_user = ?
");
$stmt->bind_param("ii", $pedidoId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pedido não encontrado.");
}

$pedido = $result->fetch_assoc();

$stmtItems = $conexao->prepare("
    SELECT pi.id_produto, pi.quantidade, pr.nome_produto, pr.preco_produto
    FROM pedido_item pi
    INNER JOIN produto pr ON pi.id_produto = pr.id_produto
    WHERE pi.id_pedido = ?
");
$stmtItems->bind_param("i", $pedidoId);
$stmtItems->execute();
$itensResult = $stmtItems->get_result();
?>

<?php include "../partials/header.php"; ?>
<?php include "../partials/navbar.php"; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Pedido Finalizado</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <strong>Pedido ID:</strong> <?php echo $pedido['id_pedido']; ?><br>
                <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?><br>
                <strong>Total:</strong> R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?><br>
                <strong>Status:</strong> <?php echo htmlspecialchars($pedido['tipo_status']); ?><br>
                <?php if ($pedido['mensagem']): ?>
                    <strong>Mensagem:</strong> <?php echo htmlspecialchars($pedido['mensagem']); ?><br>
                <?php endif; ?>
            </div>

            <h4>Endereço de Entrega:</h4>
            <p>
                <?php echo htmlspecialchars($pedido['logradouro']); ?><br>
                Bairro: <?php echo htmlspecialchars($pedido['nome_bairro']); ?><br>
                Cidade: <?php echo htmlspecialchars($pedido['nome_cidade']); ?><br>
                Estado: <?php echo htmlspecialchars($pedido['nome_estado']); ?><br>
                <?php if ($pedido['complemento']): ?>
                    Complemento: <?php echo htmlspecialchars($pedido['complemento']); ?>
                <?php endif; ?>
            </p>

            <h4>Produtos do Pedido:</h4>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $itensResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nome_produto']); ?></td>
                            <td>R$ <?php echo number_format($item['preco_produto'], 2, ',', '.'); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco_produto'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <a href="../telainicial/index.php" class="btn btn-warning">Continuar Comprando</a>
                <a href="../pagamento/new.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn btn-primary">Pagar</a>
            </div>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>
