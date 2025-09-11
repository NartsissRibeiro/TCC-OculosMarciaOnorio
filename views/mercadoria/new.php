<?php
include '../partials/header.php';
include '../partials/navbar.php';
include '../../db/conexao.php';

// Consulta fornecedores
$sql_fornecedor = "SELECT id_fornecedor, nome_fornecedor FROM fornecedor";
$result_fornecedor = $conexao->query($sql_fornecedor);

// Consulta produtos
$sql_produto = "SELECT id_produto, nome_produto FROM produto";
$result_produto = $conexao->query($sql_produto);
?>

<section class="container-form">
        <div class="card-header">
            <h2>Cadastro de Mercadoria</h2>
        </div>
        <div class="card-body">
  <?php if (SessionController::IsLoggedIn()): ?>
    <?php $iduser = SessionController::getUserId(); ?>

    <form action="../../Controller/mercadoria/new.php" method="post" enctype="multipart/form-data">
      
      <!-- Fornecedor -->
      <div class="mb-3">
        <label for="id_fornecedor" class="form-label">Fornecedor:</label>
        <select id="id_fornecedor" name="id_fornecedor" required>
          <option value="" selected disabled>Selecione o fornecedor</option>
          <?php
          if ($result_fornecedor && $result_fornecedor->num_rows > 0) {
              while ($row = $result_fornecedor->fetch_assoc()) {
                  echo "<option value='{$row['id_fornecedor']}'>{$row['nome_fornecedor']}</option>";
              }
          } else {
              echo "<option value=''>Nenhum fornecedor encontrado</option>";
          }
          ?>
        </select>
      </div>

      <!-- Produto -->
      <div class="mb-3">
        <label for="id_produto" class="form-label">Produto:</label>
        <select id="id_produto" name="id_produto" required>
          <option value="" selected disabled>Selecione o produto</option>
          <?php
          if ($result_produto && $result_produto->num_rows > 0) {
              while ($row = $result_produto->fetch_assoc()) {
                  echo "<option value='{$row['id_produto']}'>{$row['nome_produto']}</option>";
              }
          } else {
              echo "<option value=''>Nenhum produto encontrado</option>";
          }
          ?>
        </select>
      </div>

      <!-- Quantidade -->
      <div class="mb-3">
        <label for="quantidade" class="form-label">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" min="0" max="999" required>
      </div>

      <!-- Botões -->
      <div class="d-flex justify-content-between">
        <button type="reset" class="btn">Limpar</button>
        <button type="submit" class="btn">Cadastrar</button>
      </div>
    </form>

        </div>
  <?php else: ?>
    <div class="alert alert-warning" role="alert">
      Usuário não está logado. Por favor, faça login de admin para acessar essa aba.
    </div>
  <?php endif; ?>
 
</section>