<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

$id = isset($_GET['id_produto']) ? (int)$_GET['id_produto'] : 0;
if ($id == 0) {
    echo "<div class='alert alert-danger'>Produto não informado!</div>";
    exit;
}

$sql_produto = "SELECT * FROM produto WHERE id_produto = $id";
$result_produto = $conexao->query($sql_produto);
if (!$result_produto || $result_produto->num_rows == 0) {
    echo "<div class='alert alert-danger'>Produto não encontrado!</div>";
    exit;
}
$produto = $result_produto->fetch_assoc();

$sql_material = "SELECT id_material, nome_material FROM material";
$result_material = $conexao->query($sql_material);

$sql_cor = "SELECT id_cor, nome_cor FROM cor";
$result_cor = $conexao->query($sql_cor);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h2>Atualização de Produto</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['erro'])): ?>
                        <div class="alert alert-danger">Erro ao atualizar: <?= htmlspecialchars($_GET['erro']) ?></div>
                    <?php endif; ?>

                    <form action="../../controller/produto/edit.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name_produto" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="name_produto" name="name_produto"
                                    value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>
                            </div>
                             <div class="col-md-6">
                                <label for="desc_produto" class="form-label">Descrição:</label>
                                <input type="text" class="form-control" id="desc_produto" name="desc_produto"
                                    value="<?= htmlspecialchars($produto['desc_produto']) ?>" required>
                            </div>
                        <div class="row">    
                            <div class="col-md-6">
                                <label for="price_produto" class="form-label">Preço:</label>
                                <input type="number" class="form-control" id="price_produto" name="price_produto"
                                    step="0.01" min="0" max="999"
                                    value="<?= $produto['preco_produto'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="estoque_produto" class="form-label">Estoque:</label>
                                <input type="number" class="form-control" id="estoque_produto" name="estoque_produto"
                                    min="0" max="999"
                                    value="<?= $produto['estoque_produto'] ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_material" class="form-label">Material</label>
                                <select id="id_material" name="id_material" required class="form-select">
                                    <option value="">Selecione um material</option>
                                    <?php while ($row = $result_material->fetch_assoc()): ?>
                                        <option value="<?= $row['id_material'] ?>" <?= ($row['id_material'] == $produto['id_material']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nome_material']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_cor" class="form-label">Cor</label>
                                <select id="id_cor" name="id_cor" required class="form-select">
                                    <option value="">Selecione uma cor</option>
                                    <?php while ($row = $result_cor->fetch_assoc()): ?>
                                        <option value="<?= $row['id_cor'] ?>" <?= ($row['id_cor'] == $produto['id_cor']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nome_cor']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning mt-3 me-2">Atualizar</button>
                            <button type="reset" class="btn btn-secondary mt-3">Limpar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
