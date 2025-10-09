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
            <table id="dadosTable" class="table table-striped table-hover text-center align-middle">
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
                                    <a href="edit.php?id_produto=<?php echo $row['id_produto']; ?>" class="btn btn-sm btn-warning">
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
                                        <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z'/>
                                </svg></a>
                                    <a href="../../Controller/produto/Delete.php?id_produto=<?php echo $row['id_produto']; ?>" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                    <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                                </svg></a>
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
