<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

// Consulta os produtos
$sql = "SELECT p.id_produto, p.nome_produto, p.desc_produto, p.preco_produto, p.estoque_produto, 
               c.nome_cor, m.nome_material
        FROM produto p
        LEFT JOIN cor c ON p.id_cor = c.id_cor
        LEFT JOIN material m ON p.id_material = m.id_material";

$result = mysqli_query($conexao, $sql);
?>

<div class="container mt-5">
    <div class="card shadow">
           <div class="card-header">
            <h2>Consulta de Produto</h2>
        <div class="card-body">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Cor</th>
                        <th>Material</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) { 
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id_produto']; ?></td>
                                <td><?php echo $row['nome_produto']; ?></td>
                                <td><?php echo $row['desc_produto'] ?: '-'; ?></td>
                                <td>R$ <?php echo number_format($row['preco_produto'], 2, ',', '.'); ?></td>
                                <td><?php echo $row['estoque_produto']; ?></td>
                                <td><?php echo $row['nome_cor'] ?: '-'; ?></td>
                                <td><?php echo $row['nome_material'] ?: '-'; ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id_produto']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="../../Controller/produto/Delete.php?id=<?php echo $row['id_produto']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
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
