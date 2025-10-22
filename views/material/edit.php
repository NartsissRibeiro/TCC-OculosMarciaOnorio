<?php 
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

if (SessionController::isLoggedIn()) {

    // Pega o ID pela URL
    $id_material = isset($_GET['id_material']) ? (int) $_GET['id_material'] : 0;

    if ($id_material == 0) {
        echo "<div class='alert alert-danger text-center mt-5'>Material não informado!</div>";
    } else {
        // Busca o material no banco
        $sql = "SELECT * FROM material WHERE id_material = $id_material";
        $result = $conexao->query($sql);

        if (!$result || $result->num_rows == 0) {
            echo "<div class='alert alert-danger text-center mt-5'>Material não encontrado!</div>";
        } else {
            $material = $result->fetch_assoc();
            ?>
            <div class="container">
                <div class="row">
                    <div class="mt-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>Editar Material</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" action="../../controller/material/edit.php">
                                    <input type="hidden" name="id_material" value="<?php echo $material['id_material']; ?>">

                                    <div class="mb-3">
                                        <label for="nome_material" class="form-label">Nome do Material</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nome_material" 
                                               name="nome_material" 
                                               value="<?php echo $material['nome_material']; ?>" 
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
