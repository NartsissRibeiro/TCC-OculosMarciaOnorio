<?php
include "../../db/conexao.php";
include "../partials/header.php";
include "../partials/navbar.php";

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

$sql = "
    SELECT 
        DATE_FORMAT(p.data_pedido, '%Y-%m') AS mes,
        SUM(p.valor_total) AS faturamento
    FROM pedido p
    INNER JOIN status s ON p.id_status = s.id_status
    WHERE s.tipo_status = 'pago'
    GROUP BY DATE_FORMAT(p.data_pedido, '%Y-%m')
    ORDER BY mes;
";
$result2 = $conexao->query($sql);

$meses = [];
$faturamento = [];

while ($row = $result2->fetch_assoc()) {
    $meses[] = date('M/Y', strtotime($row['mes'] . '-01'));
    $faturamento[] = (float)$row['faturamento'];
}
?>

<style>
.chart-container {
    width: 100%;
    max-width: 900px;
    height: 400px;
    margin: 0 auto;
}
.carousel-control-prev, .carousel-control-next {
    filter: invert(50%);
}
.card {
    max-width: 1000px;
    margin: 0 auto;
}
</style>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Painel de Estatísticas</h3>
            <div>
                <button class="btn btn-outline-secondary btn-sm" data-bs-target="#chartCarousel" data-bs-slide="prev">
                    ‹
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-target="#chartCarousel" data-bs-slide="next">
                    ›
                </button>
            </div>
        </div>

        <div class="card-body">
            <div id="chartCarousel" class="carousel slide" data-bs-interval="false">
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <h5 class="text-center mb-4">Top 10 Produtos Mais Vendidos</h5>
                        <?php if (count($produtos) > 0): ?>
                            <div class="chart-container">
                                <canvas id="produtosChart"></canvas>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">Nenhum produto pedido ainda.</div>
                        <?php endif; ?>
                    </div>

                    <div class="carousel-item">
                        <h5 class="text-center mb-4">Faturamento Mensal</h5>
                        <?php if (count($meses) > 0): ?>
                            <div class="chart-container">
                                <canvas id="faturamentoChart"></canvas>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">Nenhum pedido pago encontrado.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctxProdutos = document.getElementById('produtosChart');
if (ctxProdutos) {
    new Chart(ctxProdutos, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($produtos); ?>,
            datasets: [{
                label: 'Quantidade Vendida',
                data: <?php echo json_encode($quantidades); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: { x: { beginAtZero: true } }
        }
    });
}

const ctxFaturamento = document.getElementById('faturamentoChart');
if (ctxFaturamento) {
    new Chart(ctxFaturamento, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($meses); ?>,
            datasets: [{
                label: 'Faturamento (R$)',
                data: <?php echo json_encode($faturamento); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                y: { beginAtZero: true },
                x: {}
            }
        }
    });
}
</script>

<?php include "../partials/footer.php"; ?>
