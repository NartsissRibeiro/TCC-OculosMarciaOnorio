<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';
include_once '../../Controller/Session/Session.php';

if (!SessionController::isLoggedIn()) {
    die("<div class='alert alert-danger text-center mt-5'>Você precisa estar logado para acessar esta página.</div>");
}

$pedidoId = $_GET['id_pedido'] ?? null;
if (!$pedidoId) {
    die("<div class='alert alert-danger text-center mt-5'>Pedido inválido.</div>");
}

// --- Busca informações do pedido ---
$stmt = $conexao->prepare("
    SELECT p.id_pedido, p.data_pedido, p.valor_total, p.complemento, p.mensagem,
           st.tipo_status, u.nome_user, u.email_user,
           l.logradouro, b.nome_bairro, c.nome_cidade, e.nome_estado
    FROM pedido p
    INNER JOIN status st ON p.id_status = st.id_status
    INNER JOIN usuarios u ON p.id_user = u.id_user
    INNER JOIN logradouro l ON p.id_logradouro = l.id_logradouro
    INNER JOIN bairro b ON p.id_bairro = b.id_bairro
    INNER JOIN cidade c ON p.id_cidade = c.id_cidade
    INNER JOIN estado e ON c.id_estado = e.id_estado
    WHERE p.id_pedido = ?
");
$stmt->bind_param("i", $pedidoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='alert alert-warning text-center mt-5'>Pedido não encontrado.</div>");
}

$pedido = $result->fetch_assoc();

// --- Busca itens do pedido ---
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

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header text-center bg-dark text-white">
            <h3>Detalhes do Pedido #<?php echo $pedido['id_pedido']; ?></h3>
        </div>
        <div class="card-body">

            <h5 class="mb-3"><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_user']); ?></h5>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email_user']); ?></p>
            <p><strong>Status:</strong> 
                <span class="badge 
                    <?php 
                        echo ($pedido['tipo_status'] == 'não pago') ? 'bg-warning' :
                             (($pedido['tipo_status'] == 'Pago') ? 'bg-success' : 'bg-secondary');
                    ?>">
                    <?php echo ucfirst($pedido['tipo_status']); ?>
                </span>
            </p>
            <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>

            <hr>
            <h5><strong>Endereço de Entrega</strong></h5>
            <p>
                <?php echo htmlspecialchars($pedido['logradouro']); ?><br>
                Bairro: <?php echo htmlspecialchars($pedido['nome_bairro']); ?><br>
                Cidade: <?php echo htmlspecialchars($pedido['nome_cidade']); ?><br>
                Estado: <?php echo htmlspecialchars($pedido['nome_estado']); ?><br>
                <?php if ($pedido['complemento']): ?>
                    Complemento: <?php echo htmlspecialchars($pedido['complemento']); ?>
                <?php endif; ?>
            </p>

            <hr>
            <h5>Itens do Pedido</h5>
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    while ($item = $itensResult->fetch_assoc()) {
                        $subtotal = $item['preco_produto'] * $item['quantidade'];
                        $total += $subtotal;
                        echo "<tr>
                                <td>" . htmlspecialchars($item['nome_produto']) . "</td>
                                <td>R$ " . number_format($item['preco_produto'], 2, ',', '.') . "</td>
                                <td>" . $item['quantidade'] . "</td>
                                <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <hr>
            <h5>Mensagem do Pedido</h5>
            <p><?php echo htmlspecialchars($pedido['mensagem'] ?? 'Nenhuma mensagem.'); ?></p>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <a href="editar_pedido_admin.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil-fill"></i> Editar Status
                </a>
            </div>

        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>

