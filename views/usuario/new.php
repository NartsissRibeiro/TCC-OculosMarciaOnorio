<?php
include '../partials/header.php'; 
include '../partials/navbar.php';
?>
    <div class="container my-5">
        <div class="container-form shadow-lg p-4 rounded" style="max-width: 500px;">
            <h2 class="text-center mb-4" style="font-weight: 400; color: var(--main-color);">
                Cadastro
            </h2>
            <?php
            if (isset($_GET['msgErro']) && !empty($_GET['msgErro'])) {
                echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_GET['msgErro']) . '</div>';
            }
            ?>
            <?php
            if (isset($_GET['msgSucesso']) && !empty($_GET['msgSucesso'])) {
                echo '<div class="alert alert-success text-center">' . htmlspecialchars($_GET['msgSucesso']) . '</div>';
            }
            ?>
            <form action="cadastrarUsuario.php" method="POST" novalidate>
                <div class="mb-2">
                    <label class="form-label text-light">Nome: </label>
                    <input type="text" name="nome" class="form-control bg-dark text-light border-secondary" placeholder="Digite seu nome" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-light">Email: </label>
                    <input type="email" name="email" class="form-control bg-dark text-light border-secondary" placeholder="Digite seu email" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-light">Telefone: </label>
                    <input type="tel" name="telefone" class="form-control bg-dark text-light border-secondary" placeholder="(xx)xxxxx-xxxx" required>
                </div>
                <div class="mb-2">
                    <label class="form-label text-light">Senha: </label>
                    <input type="password" name="senha" class="form-control bg-dark text-light border-secondary" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn w-100 py-2" style="background-color: var(--main-color); color: var(--white); border: none;">
                    Cadastrar
                </button>
            </form>
        </div>
    </div>

<?php include '../partials/footer.php'; ?>
