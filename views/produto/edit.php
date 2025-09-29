<?php
include "../partials/header.php";
include "../partials/navbar.php";

// Verifica se o ID do produto foi passado
$id = isset($_GET['id_produto']) ? (int)$_GET['id_produto'] : 0;
if ($id == 0) {
    echo "<div class='alert alert-danger'>Produto não informado!</div>";
    exit;
}

include "../../db/conexao.php";

// Consulta produto
$sql_produto = "SELECT * FROM produto WHERE id_produto = $id";
$result_produto = mysqli_query($conexao, $sql_produto);
if (!$result_produto || mysqli_num_rows($result_produto) == 0) {
    echo "<div class='alert alert-danger'>Produto não encontrado!</div>";
    exit;
}
$produto = mysqli_fetch_assoc($result_produto);

// Consulta cores
$sql_cor = "SELECT * FROM cor";
$result_cor = mysqli_query($conexao, $sql_cor);

// Consulta materiais
$sql_material = "SELECT * FROM material";
$result_material = mysqli_query($conexao, $sql_material);
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Atualização de Produto
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="name_produto" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="name_produto" name="name_produto" value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($produto['desc_produto']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="price_produto" class="form-label">Preço</label>
                    <input type="number" class="form-control" id="price_produto" name="price_produto" step="0.01" min="0" value="<?= $produto['preco_produto'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="estoque_produto" class="form-label">Estoque</label>
                    <input type="number" class="form-control" id="estoque_produto" name="estoque_produto" min="0" value="<?= $produto['estoque_produto'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="id_cor" class="form-label">Cor</label>
                    <select class="form-select" id="id_cor" name="id_cor" required>
                        <option value="">Selecione uma cor</option>
                        <?php while ($cor = mysqli_fetch_assoc($result_cor)) : ?>
                            <option value="<?= $cor['id_cor'] ?>" <?= ($cor['id_cor'] == $produto['id_cor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cor['nome_cor']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_material" class="form-label">Material</label>
                    <select class="form-select" id="id_material" name="id_material" required>
                        <option value="">Selecione um material</option>
                        <?php while ($material = mysqli_fetch_assoc($result_material)) : ?>
                            <option value="<?= $material['id_material'] ?>" <?= ($material['id_material'] == $produto['id_material']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($material['nome_material']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">

                <button type="submit" class="btn btn-warning mt-3">Atualizar</button>
            </form>
