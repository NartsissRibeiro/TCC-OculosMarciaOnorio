<?php 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

if (SessionController::isLoggedIn()) {

    // Pega o ID pela URL
    $id_cor = isset($_GET['id_cor']) ? (int) $_GET['id_cor'] : 0;

    if ($id_cor == 0) {
        echo "<div class='alert alert-danger text-center mt-5'>cor não informado!</div>";
    } else {
        $sql = "SELECT * FROM cor WHERE id_cor = $id_cor";
        $result = $conexao->query($sql);

        if (!$result || $result->num_rows == 0) {
            echo "<div class='alert alert-danger text-center mt-5'>Cor não encontrado!</div>";
        } else {
            $cor = $result->fetch_assoc();
            ?>
            <div class="container">
                <div class="row">
                    <div class="mt-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>Editar Cor</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" action="../../Controller/cor/edit.php">
                                    <input type="hidden" name="id_cor" value="<?php echo $cor['id_cor']; ?>">

                                    <div class="mb-3">
                                        <label for="nome_cor" class="form-label">Nome da Cor</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nome_cor" 
                                               name="nome_cor" 
                                               value="<?php echo $cor['nome_cor']; ?>" 
                                               required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

} else {
    echo "<div class='alert alert-warning text-center mt-5' role='alert'>
        Usuário não está logado. Por favor, faça login de admin para acessar essa aba.
    </div>";
}
?>

<?php include '../partials/footer.php'; ?>
