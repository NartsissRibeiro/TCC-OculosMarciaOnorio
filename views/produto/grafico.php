<?php
include "../../db/conexao.php";
include "../partials/header.php";
include "../partials/navbar.php";

// Busca os produtos mais vendidos
$stmt = $conexao->prepare("
    SELECT p.nome_produto, SUM(pi.quantidade) AS total_vendido
    FROM pedido_item pi
    INNER JOIN produto p ON pi.id_produto = p.id_produto
    GROUP BY p.id_produto, p.nome_produto
    ORDER BY total_vendido DESC
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
$quantidades = [];

while ($row = $result->fetch_assoc()) {
    $produtos[] = $row['nome_produto'];
    $quantidades[] = (int)$row['total_vendido'];
}
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="text-center mb-0">Produtos Mais Pedidos</h3>
        </div>
        <div class="card-body">
            <?php if (count($produtos) > 0): ?>
                <canvas id="produtosChart" style="width:100%; max-height:400px;"></canvas>
            <?php else: ?>
                <div class="alert alert-warning text-center">Nenhum produto pedido ainda.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('produtosChart').getContext('2d');
const produtosChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($produtos); ?>,
        datasets: [{
            label: 'Quantidade Pedida',
            data: <?php echo json_encode($quantidades); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y', // barra horizontal
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Top 10 Produtos Mais Pedidos'
            }
        },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>

<?php
include "../partials/footer.php";
?>
