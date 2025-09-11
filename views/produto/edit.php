<?php
include "../partials/header.php";
include "../partials/navbar.php";
$id = $_GET['id_produto'];
?>
<div class="container">
    <div class="card">
        <div class="card-header">
            Atualização De produto
        </div>
        <div class="card-body">
            <form method='get' action='../../controller/produto/edit.php'>
                <?php
                include "../../db/conexao.php";
                $sql = "SELECT * FROM produto WHERE id_produto = $id;";
                $sql .= "SELECT * FROM cor;";
                $sql .= "SELECT * FROM material;";

                if (mysqli_multi_query($conexao, $sql)) {
                    // Primeira consulta (SELECT * FROM produto WHERE id_prod = $id)
                    if ($result = mysqli_store_result($conexao)) {
                        if (mysqli_num_rows($result) > 0) {
                            // O produto deve existir, portanto vamos capturar o id_prod aqui
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<div class='mb-3'>
                                        <label for='text' class='form-label'>Nome do Produto: </label>
                                        <input type='text' class='form-control' id='name_produto' name='name_produto' value='" . $row['nome_produto'] . "'>
                                    </div>";
                                echo "<div class='mb-3'>
                                        <label for='text' class='form-label'>Descrição: </label>
                                        <input type='text' class='form-control' id='description' name='description' value='" . $row['desc_produto'] . "'>
                                    </div>";
                                echo "<div class='mb-3'>
                                        <label for='price' class='form-label'>Preço: </label>
                                        <input type='number' class='form-control' id='price_produto' name='price_produto' step='0.01' min='0' max='999'value=" . $row['price_produto'] . "'>
                                    </div>";
                                echo "<div class='mb-3'>
                                        <label for='price' class='form-label'>Preço: </label>
                                        <input type='number' class='form-control' id='estoque_produto' name='estoque_produto' min='0' max='999'value=" . $row['estoque_produto'] . "'>
                                    </div>";
                                echo "<input type='hidden' name='id_produto' value='" . $row['id_produto'] . "'>";
                            }
                        }
                        mysqli_free_result($result);
                    }

                    // move para a próxima consulta (SELECT * FROM categoria)
                    if (mysqli_next_result($conexao)) {
                        if ($result_cor = mysqli_store_result($conexao)) {
                            if (mysqli_num_rows($result_cor) > 0) {
                                echo "<select id='id_cor' name='id_cor' required class='form-select'>";
                                echo "<option value='' selected>Selecione uma cor: </option>";
                                while ($row = mysqli_fetch_assoc($result_cor)) {
                                    // Processa os dados da tabela 'cor'
                                    echo "<option value='" . $row['id_cor'] . "'>" . $row['nome_cor'] . "</option>";
                                }
                                echo "</select>";
                            }
                            mysqli_free_result($result_cor);
                        }
                        if ($result_material = mysqli_store_result($conexao)) {
                            if (mysqli_num_rows($result_material) > 0) {
                                echo "<select id='id_material' name='id_material' required class='form-select'>";
                                echo "<option value='' selected>Selecione um Material: </option>";
                                while ($row = mysqli_fetch_assoc($result_material))
                                    echo "<option value='" . $row['id_material'] . "'>" . $row['nome_material'] . "</option>";
                                }
                                echo "</select>";
                            }
                            mysqli_free_result($result_material);
                    }
                } else {
                    echo "Erro: " . mysqli_error($conexao);
                }
                mysqli_close($conexao);
                ?>
                <button type='submit' class='btn btn-warning mt-3'>Atualizar</button>
            </form>
        </div>
    </div>
</div>
<?php 
include "../partials/footer.php";
?>
