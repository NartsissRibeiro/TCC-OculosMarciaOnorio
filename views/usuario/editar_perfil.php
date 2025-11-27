<?php 
include "../partials/header.php";
include "../partials/navbar.php";
require_once "../../db/conexao.php";

if (!SessionController::isLoggedIn()) {
    header('Location: ../usuario/index.php');
    exit();
}

$userId = SessionController::getUserId();

$stmt = $conexao->prepare("SELECT nome_user, email_user FROM usuarios WHERE id_user = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($nomeUser, $emailUser);
$stmt->fetch();
$stmt->close();
?>

    <div class="conainer my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h4>Editar Perfil</h4>
                    </div>

                <?php if (isset($_GET['success']) && $_GET['success'] === 'profile_updated'): ?>
                    <div class="alert alert-success text-center">
                        Perfil atualizado com sucesso!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <?php if ($_GET['error'] === 'empty_fields'): ?>
                        <div class="alert alert-danger text-center">
                            Preencha todos os campos obrigatórios!
                        </div>
                    <?php elseif ($_GET['error'] === 'update_failed'): ?>
                        <div class="alert alert-danger text-center">
                             Não foi possível atualizar o perfil. Tente novamente mais tarde.
                         </div>
                    <?php endif; ?> 
                <?php endif; ?>

                <div class="card-body">
                    <form action="../../controller/usuario/edit.php" method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($nomeUser); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($emailUser); ?>" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="perfil.php" class="btn btn-secondary">Voltar</a>
                            <button type="submit" class="btn btn-success">Salvar Alteração</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include "../partials/footer.php"; 
?>