<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

$sql = "SELECT m.id_mercadoria, m.data_hora, f.nome_fornecedor, p.nome_produto, m.quantidade
        FROM mercadoria m
        LEFT JOIN fornecedor f ON m.id_fornecedor = f.id_fornecedor
        LEFT JOIN produto p ON m.id_produto = p.id_produto";

$result = mysqli_query($conexao, $sql);
?>

<div class="container mt-5">
    <div class="card shadow">
           <div class="card-header">
            <h2>Consulta de Mercadoria</h2>
        <div class="card-body">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Data de entrada</th>
                        <th>Fornecedor</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) { 
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id_mercadoria']; ?></td>
                                <td><?php echo $row['data_hora']; ?></td>
                                <td><?php echo $row['id_fornecedor'] ?: '-'; ?></td>
                                <td><?php echo $row['id_produto']; ?></td>
                                <td><?php echo $row['quantidade'] ?: '-'; ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id_mercadoria']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="../../Controller/mercadoria/Delete.php?id=<?php echo $row['id_mercadoria']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                                </td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Nenhum produto encontrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>