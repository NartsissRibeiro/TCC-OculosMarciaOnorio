<?php 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';
?>
<div class="container">
    <div class="row">
        <div class="mt-5">
            <div class="card">
                <div class="card-header">
                    <h4>Adicionar Fornecedor</h4>
                </div>
                <div class="card-body">

                    <!-- Mensagem de sucesso/erro -->
                    <?php if (isset($_SESSION['msg'])) { ?>
                        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> mt-3" role="alert">
                            <?php echo $_SESSION['msg']; ?>
                        </div>
                        <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
                    <?php } ?>

                    <?php 
                    if(SessionController::IsloggedIn()) {
                        $iduser = SessionController::GetuserId();
                    ?>
                    <form method="post" action="../../Controller/fornecedor/New.php">
                        <div class="mb-3">
                            <label for="name_fornecedor" class="form-label">Nome do fornecedor: </label>
                            <input type="text" class="form-control" id="name_fornecedor" name="name_fornecedor" placeholder="Digite o nome do fornecedor" required>
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
</div>
<?php
include "../partials/footer.php";
?>
