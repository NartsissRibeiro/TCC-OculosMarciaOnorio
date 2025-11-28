<?php
include "../partials/header.php";
include "../partials/navbar.php";
require_once "../../db/conexao.php";
date_default_timezone_set('America/Sao_Paulo');

if (!SessionController::isLoggedIn()) {
    header('Location: ../usuario/index.php');
    exit();
}

$userId = SessionController::getUserId();
$userName = SessionController::getUserName();
$userType = SessionController::getUserTipo();

$stmt = $conexao->prepare("SELECT email_user FROM usuarios WHERE id_user = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($emailUser);
$stmt->fetch();
$stmt->close();
?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4>Perfil de <?php echo ucfirst(htmlspecialchars($userName)); ?></h4>
                    </div>
                    <div class="card-body">                        
                        <?php
                            if (isset($_GET['error'])) {
                                switch ($_GET['error']) {
                                    case 'empty_password_fields':
                                        $errorMessage = 'Todos os campos de senha são obrigatórios.';
                                        break;
                                    case 'password_mismatch':
                                        $errorMessage = 'A nova senha e a confirmação não correspondem.';
                                        break;
                                    case 'wrong_password':
                                        $errorMessage = 'A senha atual está incorreta.';
                                        break;
                                    case 'update_failed':
                                        $errorMessage = 'Ocorreu um erro ao atualizar a senha. Tente novamente.';
                                        break;
                                    default:
                                        $errorMessage = '';
                                        break;
                                }
                                echo "<div class='alert alert-danger'>$errorMessage</div>";
                            }

                            if (isset($_GET['success'])) {
                                if ($_GET['success'] == 'password_updated') {
                                    echo "<div class='alert alert-success'>Senha atualizada com sucesso!</div>";
                                }
                            }
                        ?>
                        <h5 class="card-title">Informações do Usuário</h5>
                        <p><strong>Nome:</strong> <?php echo ucfirst(htmlspecialchars($userName)); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($emailUser); ?></p>
                        <p><strong>Tipo de Usuário:</strong> <?php echo ucfirst(htmlspecialchars($userType)); ?></p>
                        
                        <a href="editar_perfil.php" class="btn btn-warning mt-3">Editar Perfil</a>
                        <a href="editar_endereco.php" class="btn btn-primary mt-3">Editar Endereço</a>

                        <h5 class="card-title mt-4">Alterar Senha</h5>
                        <form action="../../controller/usuario/edit_senha.php" method="POST">
                            <div class="mb-3">
                                <label for="senhaAtual" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" required>
                            </div>
                            <div class="mb-3">
                                <label for="novaSenha" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="novaSenha" name="novaSenha" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmaSenha" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha" required>
                            </div>
                            <button type="submit" class="btn btn-success">Atualizar Senha</button>
                        </form>
                    </div>
                <div class="card-footer text-muted text-center">
                    Último acesso: <?php echo date('d/m/Y H:i:s'); ?>
                    <a href="../telainicial/index.php" class="btn btn-secondary">Voltar</a>
                    <a href="?logout=true" class="btn btn-danger">Sair</a>
                </div>
            </div>
        </div>
    </div>
 </div>                         
<?php
include "../partials/header.php";
?>