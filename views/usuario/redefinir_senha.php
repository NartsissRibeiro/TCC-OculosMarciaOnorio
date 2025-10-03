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
                $stmt = $conexao->prepare("UPDATE usuarios SET senha_user = ?, reset_token = NULL, reset_token_expire = NULL WHERE id_user = ?");
                $stmt->bind_param('si', $novaSenhaHash, $userId);
                $stmt->execute();

                header('Location: index.php?senha=sucesso');
                exit();
            }

            echo "<div class='container mt-5'>
                <form class='login-form p-4 border rounded shadow-sm' method='POST' action=''>";
            echo "<h2 class='mb-4 text-center'>Redefinição de Senha</h2>
                         <div class='mb-3'>
                            <label for='exampleInputEmail1' class='form-label'>Nova Senha</label>
                            <input type='password' class='form-control' id='exampleInputEmail1' name='novaSenha' id='novaSenha'aria-describedby='emailHelp' required>
                         </div>
                        <button type='submit' class='btn btn-primary w-100'>Redefinir Senha</button>
                 </form>
        </div>";
        } else {
            echo 'O token expirou. Solicite um novo link para redefinição.';
        }
    } else {
        echo 'Token inválido.';
    }

    $stmt->close();
} else {
    echo 'Token não encontrado.';
}

$conexao->close();
?>
<?php
include "../partials/footer.php";
?>

