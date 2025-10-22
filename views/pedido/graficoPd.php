<?php
include "../../db/conexao.php";
include "../partials/header.php";
include "../partials/navbar.php";

$sqlVendedoresPedidos = "
    SELECT u.nome_user, COUNT(p.id_pedido) AS total_pedidos
    FROM pedido p
    INNER JOIN usuarios u ON p.id_user = u.id_user
    GROUP BY u.id_user
    ORDER BY total_pedidos DESC
    LIMIT 10;
";
$resultVendedoresPedidos = $conexao->query($sqlVendedoresPedidos);
$vendedoresPedidos = [];
$totalPedidos = [];
while ($row = $resultVendedoresPedidos->fetch_assoc()) {
    $vendedoresPedidos[] = $row['nome_user'];
    $totalPedidos[] = (int)$row['total_pedidos'];
}

$sqlVendedoresGastos = "
    SELECT u.nome_user, SUM(p.valor_total) AS total_gasto
    FROM pedido p
    INNER JOIN usuarios u ON p.id_user = u.id_user
    GROUP BY u.id_user
    ORDER BY total_gasto DESC
    LIMIT 10;
";
$resultVendedoresGastos = $conexao->query($sqlVendedoresGastos);
$vendedoresGastos = [];
$totalGastos = [];
while ($row = $resultVendedoresGastos->fetch_assoc()) {
    $vendedoresGastos[] = $row['nome_user'];
    $totalGastos[] = (float)$row['total_gasto'];
}
?>

<style>
.chart-container { width: 100%; max-width: 900px; height: 400px; margin: 0 auto; }
.carousel-control-prev, .carousel-control-next { filter: invert(50%); }
.card { max-width: 1000px; margin: 20px auto; }
</style>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Clientes</h3>
        <div>
            <button class="btn btn-outline-secondary btn-sm" data-bs-target="#carousel2" data-bs-slide="prev">‹</button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-target="#carousel2" data-bs-slide="next">›</button>
        </div>
    </div>
    <div class="card-body">
        <div id="carousel2" class="carousel slide" data-bs-interval="false">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <h5 class="text-center mb-4">Clientes com Mais Pedidos</h5>
                    <div class="chart-container"><canvas id="vendedoresPedidosChart"></canvas></div>
                </div>
                <div class="carousel-item">
                    <h5 class="text-center mb-4">Clientes que Mais Gastaram</h5>
                    <div class="chart-container"><canvas id="vendedoresGastosChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

new Chart(document.getElementById('vendedoresPedidosChart'), {
    type:'bar',
    data:{
        labels: <?php echo json_encode($vendedoresPedidos); ?>,
        datasets:[{ label:'Total de Pedidos', data: <?php echo json_encode($totalPedidos); ?>, backgroundColor:'rgba(255,159,64,0.7)', borderColor:'rgba(255,159,64,1)', borderWidth:1 }]
    },
    options:{ responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{ x:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
});

new Chart(document.getElementById('vendedoresGastosChart'), {
    type:'bar',
    data:{
        labels: <?php echo json_encode($vendedoresGastos); ?>,
        datasets:[{ label:'Total Gasto (R$)', data: <?php echo json_encode($totalGastos); ?>, backgroundColor:'rgba(255,99,132,0.7)', borderColor:'rgba(255,99,132,1)', borderWidth:1 }]
    },
    options:{ responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{ x:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
});
</script>

<?php include "../partials/footer.php"; ?>
