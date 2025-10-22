<?php 
include '../partials/header.php';
include '../partials/navbar.php';
?>
    <div class="container">
        <div class="row">
            <div class="mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Adicionar material</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        include '../../db/conexao.php';
                    if (SessionController::isLoggedIn()) {
                        $userId = SessionController::getUserId();
                        ?>
                        <form method="post" action="../../Controller/material/new.php">
                            <div class="mb-3">
                                <label for="name_material" class="form-label">Nome do material</label>
                                <input type="text" class="form-control" id="name_material" name="name_material" placeholder="Digite o nome do material" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
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
include "../partials/footer.php";
?>