<?php
session_start();
require_once '../../Controller/session/session.php';
include "../../db/conexao.php"; 
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']); 
    //consulta ao bancod de dados para ver o email
    $stmt = $conexao->prepare('SELECT id_user, nome_user, email_user, telefone_user, senha_user, tipo_user FROM usuarios WHERE email_user = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    // verifica se o usuario existe
    if ($stmt->num_rows > 0) {
    $stmt->bind_result($iduser, $name, $email, $telefone, $senhaHash, $tipo);
        $stmt->fetch();

        // verifica se o hash é o mesmo da senha
        if (password_verify($senha, $senhaHash)) {
            SessionController::login($iduser, $name, $email, $tipo);
            header('Location: ../telainicial/index.php');
            exit();
        } else {
            $loginError = 'Senha incorreta.';
        }
    } else {
        $loginError = 'Usuário não encontrado.';
    }

    $stmt->close();
}
$conexao->close();
?>
  <!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Márcia Onorio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
  </head>
  <body>
  <?php include '../partials/navbar.php'; ?>

<main class="primeiro" style="max-width: 500px; margin: 120px auto; padding: 2rem; background-color: #1c1c1c; border-radius: var(--radius); box-shadow: 0 0 15px rgba(255, 192, 203, 0.15); font-size: 1.4rem;">
  <form method="POST" action="">
   <h2 class="text-center mb-4" style="font-size: 2.4rem; font-weight: 600; color: var(--main-color);">Login</h2>
    <?php if (!empty($loginError)): ?>
      <div class="alert alert-danger text-center"><?= $loginError ?></div>
    <?php endif; ?>
    <?php
          if (isset($_GET['msgSucesso']) && !empty($_GET['msgSucesso'])) {
            echo '<div class="alert alert-success text-center">' . htmlspecialchars($_GET['msgSucesso']) . '</div>';
            }?>
    <div class="mb-2">
      <label class="form-label text-light">Email</label>
      <input type="email" name="email" placeholder="Digite seu e-mail" class="form-control bg-dark text-light border-secondary" required>
    </div>

    <div class="mb-2">
      <label class="form-label text-light">Senha</label>
      <input type="password" name="senha" placeholder="Digite sua senha" 
             class="form-control bg-dark text-light border-secondary" required>
    </div>

    <div class="link-um d-flex justify-content-between align-items-center">
      <a href="recuperacao-senha/Redefinirsenha.html" style="color: var(--main-color); text-decoration: none;">Esqueci a senha</a>
    </div>

    <button type="submit" class="btn w-100 py-2" 
            style="background-color: var(--main-color); color: var(--white); border: none; padding: 1.2rem;">
      Login
    </button>

    <div class="link-dois text-center mt-2">
      <p class="m-0">Não tem conta? <a href="new.php" style="color: var(--main-color);">Cadastrar-se</a></p>
      <p class="m-0"><a href="../telainicial/index.php" style="color: var(--main-color);">Navegar sem login</a></p>
    </div>
  </form>
</main>

<?php include '../partials/footer.php'; ?>
