<?php require_once '../../Controller/session/session.php'; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="../telainicial/index.php">
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
        <li class="nav-item"><a class="nav-link" href="../telainicial/index.php#about">Sobre</a></li>

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
      <form class="d-flex ms-3" role="search" method="get" action="../produto/pesquisa.php">
    <input class="form-control" type="search" name="q" placeholder="Pesquisar produto" aria-label="Search"/>
    <button class="btn btn-outline-light ms-2" type="submit">Buscar</button>
  </form>
    </div>
  </div>
</nav>