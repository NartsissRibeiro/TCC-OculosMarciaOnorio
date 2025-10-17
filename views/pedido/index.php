<?php 
session_start();
include "../partials/header.php";
include "../partials/navbar.php";
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";

if (!SessionController::isLoggedIn()) {
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
                Acesso negado. Esta página é restrita a administradores.
            </div>
          </div>";
    include "../partials/footer.php";
    exit;
}
?>
<?php
$sql = "
    SELECT 
        p.id_pedido, 
        p.data_pedido, 
        p.valor_total, 
        st.tipo_status,
        l.logradouro, 
        b.nome_bairro, 
        c.nome_cidade, 
        e.nome_estado, 
        p.complemento, 
        p.mensagem,
        u.nome_user AS nome_usuario, 
        u.email_user
    FROM pedido p
    INNER JOIN status st ON p.id_status = st.id_status
    INNER JOIN logradouro l ON p.id_logradouro = l.id_logradouro
    INNER JOIN bairro b ON p.id_bairro = b.id_bairro
    INNER JOIN cidade c ON p.id_cidade = c.id_cidade
    INNER JOIN estado e ON c.id_estado = e.id_estado
    INNER JOIN usuarios u ON p.id_user = u.id_user
    ORDER BY p.data_pedido DESC
";

$result = mysqli_query($conexao, $sql);
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="m-0">Consulta de Pedidos</h2>
            <a href="graficoPd.php" class="btn btn-outline-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill me-1" viewBox="0 0 16 16">
                    <path d="M1 11h2v5H1v-5zm4-4h2v9H5V7zm4-5h2v14H9V2zm4 8h2v6h-2v-6z"/>
                </svg>
                Estatísticas
            </a>
        </div>
        <div class="card-body">
            <table id="dadosTable" class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Data</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th>Endereço</th>
                        <th>Complemento</th>
                        <th>Mensagem</th>
                        <th>ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) { 
                        while ($row = mysqli_fetch_assoc($result)) { 
                            $endereco = htmlspecialchars($row['logradouro']) . ", " .
                                        htmlspecialchars($row['nome_bairro']) . " - " .
                                        htmlspecialchars($row['nome_cidade']) . "/" .
                                        htmlspecialchars($row['nome_estado']);
                        ?>
                            <tr>
                                <td><?php echo $row['id_pedido']; ?></td>
                                <td><?php echo htmlspecialchars($row['nome_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($row['email_user']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['data_pedido'])); ?></td>
                                <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                            echo ($row['tipo_status'] == 'não pago') ? 'bg-warning' :
                                                 (($row['tipo_status'] == 'pago') ? 'bg-success' : 'bg-secondary');
                                        ?>">
                                        <?php echo ucfirst($row['tipo_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $endereco; ?></td>
                                <td><?php echo htmlspecialchars($row['complemento'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['mensagem'] ?? '-'); ?></td>
                                <td>
                                    <a href="detalhes_pedido.php?id_pedido=<?php echo $row['id_pedido']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Nenhum pedido encontrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
  $(document).ready(function() {
    $('#dadosTable').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      ordering: true,
      searching: true,
      language: {
          url: "../../assets/json/pt-BR.json"
      }
    });
  });
</script>

<?php include "../partials/footer.php"; ?>
