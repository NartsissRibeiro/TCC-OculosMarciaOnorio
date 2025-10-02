<?php
require_once '../../db/conexao.php'; 
require_once '../../controller/MailController.php'; 
include "../partials/header.php";
include "../partials/navbar.php";

$successMessage = '';
$errorMessage = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conexao->prepare('SELECT id_user, email_user FROM usuarios WHERE email_user = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $userEmail);
            $stmt->fetch();
            
            $token = bin2hex(random_bytes(50));
            $expireTime = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $conexao->prepare('UPDATE usuarios SET reset_token = ?, reset_token_expire = ? WHERE email_user = ?');
            $stmt->bind_param('sss', $token, $expireTime, $email);
            $stmt->execute();

            $resetLink = "https://localhost/TCC-OculosMarciaOnorio/views/usuario/redefinir_senha.php?token=" . $token;
            $subject = "Recuperação de Senha";
            $body = "Clique no link abaixo para redefinir sua senha: <a href='$resetLink'>$resetLink</a>";

            try {
                MailController::sendMail($email, $subject, $body);
                $successMessage = "Um e-mail de recuperação foi enviado para $email. Verifique sua caixa de entrada.";
            } catch (Exception $e) {
                $errorMessage = "Erro ao enviar o e-mail. Tente novamente mais tarde."; 
            }

        } else {
            $errorMessage = "E-mail não encontrado. Por favor, tente novamente.";
        }

        $stmt->close();
    } else {
        $errorMessage = "E-mail inválido.";
    }
}
?>

<main class="primeiro" style="max-width: 500px; margin: 120px auto; padding: 2rem; background-color: #1c1c1c; border-radius: var(--radius); box-shadow: 0 0 15px rgba(255, 192, 203, 0.15); font-size: 1.4rem;">
  <form method="POST" action="">
    <h2 class="text-center mb-4" style="font-size: 2.4rem; font-weight: 600; color: var(--main-color);">Recuperação de Senha</h2>

    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success text-center"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
      <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label text-light">E-mail</label>
      <input type="email" name="email" placeholder="Digite seu e-mail" class="form-control bg-dark text-light border-secondary" required>
    </div>

    <button type="submit" class="btn w-100 py-2" style="background-color: var(--main-color); color: var(--white); border: none; padding: 1.2rem;">
      <h5>Enviar Link de Recuperação</h5>
    </button>

    <div class="link-dois text-center mt-2">
      <p class="m-0"><a href="index.php" style="color: var(--main-color);">Voltar para o login</a></p>
      <p class="m-0"><a href="../telainicial/index.php" style="color: var(--main-color);">Navegar sem login</a></p>
    </div>
  </form>
</main>
<?php include '../partials/footer.php'; ?>
