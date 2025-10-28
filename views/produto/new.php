<?php
   include '../partials/header.php';
   include '../partials/navbar.php';
   include '../../db/conexao.php'; 

    $sql_material = "SELECT id_material, nome_material FROM material";
    $result_material = $conexao->query($sql_material);

    $sql_cor = "SELECT id_cor, nome_cor FROM cor";
    $result_cor = $conexao->query($sql_cor);
    ?>
    <div class="container mt-5">
        <div class="row">
            <div>
                <div class="card">
                    <div class="card-header">
                        <h2>Cadastro de Produto</h2>
                    </div>
                    <div class="card-body">
                        <?php
            include "../../db/conexao.php";
           if (SessionController::isLoggedIn()) {
            $iduser = SessionController::getUserId();
    ?>
                        <form action="../../Controller/produto/New.php" method="post" enctype="multipart/form-data"> 
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nome: </label>
                                    <input type="text" class="form-control" id="name_produto" name="name_produto" required>
                                </div>

                                    <div class="col-md-6">
                                    <label for="desc" class="form-label">Descrição: </label>
                                    <input type="text" class="form-control" id="desc_produto" name="desc_produto" required>
                                </div>
                            </div>
                            
                            <div class="row">    
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Preço: </label>
                                    <input type="number" class="form-control" id="price_produto" name="price_produto" step="0.01" min="0" max="999" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="estoque" class="form-label">Estoque: </label>
                                    <input type="number" class="form-control" id="estoque" name="estoque" min="0" max="999" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="id_material" class="form-label">Material</label>
                                    <select id="id_material" name="id_material" required class="form-select">
                                        <option value="" selected>Selecione um material: </option>
                                        <?php
                                        if ($result_material->num_rows > 0) {
                                            while ($row = $result_material->fetch_assoc()) {
                                                echo "<option value='" . $row['id_material'] . "'>" . $row['nome_material'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>Nenhuma categoria encontrada</option>";
                                        } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_cor" class="form-label">Cor</label>
                                    <select id="id_cor" name="id_cor" required class="form-select">
                                        <option value="" selected>Selecione uma cor: </option>
                                        <?php
                                        if ($result_cor->num_rows > 0) {
                                            while ($row = $result_cor->fetch_assoc()) {
                                                echo "<option value='" . $row['id_cor'] . "'>" . $row['nome_cor'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>Nenhuma cor encontrada</option>";
                                        } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="image" class="form-label">Imagem do Produto:</label>
                                    <input type="file" name="image" id="image" accept="image/*" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mt-3 mb-1 me-2">Cadastrar</button>
                                <button type="reset" class="btn btn-secondary mt-3 mb-1 me-2">Limpar</button>
                            </div>
                        </form>
                        <?php } 
                        else {
                           echo "<div class='alert alert-warning' role='alert'>
                           Usuário não está logado. Por favor, faça login de admin para acessar essa aba.
                           </div>"; 
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
include '../partials/footer.php';
?>
