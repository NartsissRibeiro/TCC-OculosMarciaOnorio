<?php 
include '../partials/header.php';
include '../partials/navbar.php';
?>
    <div class="container">
        <div class="row">
            <div class="mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Adicionar Cor</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(SessionController::IsloggedIn()) {
                            $iduser = SessionController::GetuserId();
                        ?>
                        <form method="post" action="../../Controller/cor/new.php">
                            <div class="mb-3">
                                <label for="name_cor" class="form-label">Nome da Cor</label>
                                <input type="text" class="form-control" id="name" name="name_cor" placeholder="Digite o nome da cor" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                        <?php } 
                        else {
                            echo "<div class='alert alert-warning' role='alert'>
                            Usuário não está logado. Por favor, faça login de admin para acessar essa aba.
                            </div>";
                        } ?>

                </div>
            </div>
        </div>
    </div>
<?php
include "../partials/footer.php";
?>