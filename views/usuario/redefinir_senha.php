<?php
require '../../db/conexao.php';
include "../partials/header.php";
include "../partials/navbar.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se o token existe e se não expirou
    $stmt = $conexao->prepare("SELECT id_user, reset_token_expire FROM usuarios WHERE reset_token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $resetTokenExpiry);
        $stmt->fetch();

        // Verifica se o token ainda não expirou
        if ($resetTokenExpiry >= time()) {
            // Se o token for válido, o usuário pode redefinir a senha
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $novaSenha = trim($_POST['novaSenha']);
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Atualiza a senha do usuário
                $stmtUpdate = $conexao->prepare("UPDATE usuarios SET senha_user = ?, reset_token = NULL, reset_token_expire = NULL WHERE id_user = ?");
                $stmtUpdate->bind_param('si', $novaSenhaHash, $userId);
                $stmtUpdate->execute();

                header('Location: index.php?senha=sucesso');
                exit();
            }
            ?>

            <main class="primeiro" style="max-width: 500px; margin: 120px auto; padding: 2rem; background-color: #1c1c1c; border-radius: var(--radius); box-shadow: 0 0 15px rgba(255, 192, 203, 0.15); font-size: 1.4rem;">
                <form method="POST" action="">
                    <h2 class="text-center mb-4" style="font-size: 2.4rem; font-weight: 600; color: var(--main-color);">Redefinição de Senha</h2>
                    
                    <div class="mb-2">
                        <label class="form-label text-light" for="novaSenha">Nova Senha</label>
                        <input type="password" name="novaSenha" id="novaSenha" placeholder="Digite sua nova senha" class="form-control bg-dark text-light border-secondary" required>
                    </div>

                    <button type="submit" class="btn w-100 py-2" style="background-color: var(--main-color); color: var(--white); border: none; padding: 1.2rem;">
                        <h5>Redefinir Senha</h5>
                    </button>

                    <div class="link-dois text-center mt-2">
                        <p class="m-0"><a href="login.php" style="color: var(--main-color);">Voltar ao login</a></p>
                        <p class="m-0"><a href="../telainicial/index.php" style="color: var(--main-color);">Navegar sem login</a></p>
                    </div>
                </form>
            </main>

            <?php
        } else {
            echo '<div class="alert alert-danger text-center mt-5">O token expirou. Solicite um novo link para redefinição.</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center mt-5">Token inválido.</div>';
    }

    $stmt->close();
} else {
    echo '<div class="alert alert-danger text-center mt-5">Token não encontrado.</div>';
}

$conexao->close();
include "../partials/footer.php";
?>
