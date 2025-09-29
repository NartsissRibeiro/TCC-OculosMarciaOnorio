<?php
include '../partials/header.php'; 
include '../partials/navbar.php';
?>
<main class="primeiro" style="max-width: 500px; margin: 40px auto 20px auto; padding: 2rem; background-color: #1c1c1c; border-radius: var(--radius); box-shadow: 0 0 15px rgba(255, 192, 203, 0.15); font-size: 1.4rem;">
    <form action="cadastrarUsuario.php" method="POST" novalidate>
        <h2 class="text-center mb-4" style="font-size: 2.4rem; font-weight: 600; color: var(--main-color);">
            Cadastro
        </h2>

        <?php
        if (isset($_GET['msgErro']) && !empty($_GET['msgErro'])) {
            echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_GET['msgErro']) . '</div>';
        }

        if (isset($_GET['msgSucesso']) && !empty($_GET['msgSucesso'])) {
            echo '<div class="alert alert-success text-center">' . htmlspecialchars($_GET['msgSucesso']) . '</div>';
        }
        ?>

        <div class="mb-2">
            <label class="form-label text-light">Nome:</label>
            <input type="text" name="nome" placeholder="Digite seu nome" class="form-control bg-dark text-light border-secondary" required>
        </div>

        <div class="mb-2">
            <label class="form-label text-light">Email:</label>
            <input type="email" name="email" placeholder="Digite seu email" class="form-control bg-dark text-light border-secondary" required>
        </div>

        <div class="mb-2">
            <label class="form-label text-light">Telefone:</label>
            <input type="tel" name="telefone" placeholder="(xx)xxxxx-xxxx" class="form-control bg-dark text-light border-secondary" required>
        </div>

        <div class="mb-2">
            <label class="form-label text-light">Senha:</label>
            <input type="password" name="senha" placeholder="Digite sua senha" class="form-control bg-dark text-light border-secondary" required>
        </div>

        <button type="submit" class="btn w-100 py-2" style="background-color: var(--main-color); color: var(--white); border: none;">
            <h5>Cadastrar</h5>
        </button>

        <div class="link-dois text-center mt-2">
            <p class="m-0">Já tem conta? <a href="index.php" style="color: var(--main-color);">Login</a></p>
            <p class="m-0"><a href="../telainicial/index.php" style="color: var(--main-color);">Navegar sem login</a></p>
        </div>
    </form>
</main>

<?php include '../partials/footer.php'; ?>
