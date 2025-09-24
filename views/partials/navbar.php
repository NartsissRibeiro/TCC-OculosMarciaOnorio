<?php require_once '../../Controller/session/session.php'; ?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
  </head>
  <body>
    <?php 
?>
<nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="aparte/Site.Marcia/index.html">
      <img src="../../assets/img/logo-invertido-removebg-preview copy.png" alt="Logo" width="200" height="50">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <?php if (SessionController::isLoggedIn()): ?>
          <span class="me-3 text-capitalize fs-5">
            Olá, <strong><?= htmlspecialchars(SessionController::getUserName()) ?></strong>!</span>
      <?php endif; ?>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../telainicial/index.php">Página inicial</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">Sobre</a></li>

        <?php if (SessionController::getUserTipo() === 'admin'): ?>
          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Cadastrar
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../produto/new.php">Produto</a></li>
            <li><a class="dropdown-item" href="../material/new.php">Material</a></li>
            <li><a class="dropdown-item" href="../cor/new.php">Cor</a></li>
            <li><a class="dropdown-item" href="../fornecedor/new.php">Fornecedor</a></li>
            <li><a class="dropdown-item" href="../mercadoria/new.php">Mercadoria</a></li>
        </ul>
        </li>
          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Consultar
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../produto/index.php">Produto</a></li>
            <li><a class="dropdown-item" href="../material/.php">Material</a></li>
            <li><a class="dropdown-item" href="index.php">Cor</a></li>
            <li><a class="dropdown-item" href="index.php">Fornecedor</a></li>
            <li><a class="dropdown-item" href="index.php">Mercadoria</a></li>
            <li><a class="dropdown-item" href="index.php">Pedido</a></li>
            <li><a class="dropdown-item" href="index.php">Venda</a></li>
        </ul>
        </li>
        <?php endif; ?>

        <?php if (SessionController::getUserTipo() === 'cliente'): ?>
           <li class="nav-item"><a class="nav-link" href="../usuario/pedido.php">Meus Pedidos</a></li>
          <li class="nav-item"><a class="nav-link" href="#perfil">Perfil</a></li>
          <?php endif; ?>

      <?php if (SessionController::isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link" href="?logout=true">Sair</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../usuario/index.php">Login</a></li>
        <?php endif; ?>
      </ul>
      <a class="ms-3" href="../carrinho/index.php"><img src="../../assets/img/shopping-cart--v1.png" alt="Carrinho" class="img-carrinho"></a>
    </div>
  </div>
</nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  </body>
</html>