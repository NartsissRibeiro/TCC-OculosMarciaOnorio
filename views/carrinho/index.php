<?php
include "../partials/header.php";
include "../partials/navbar.php";
require_once "../../controller/session/session.php";

if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $id => $produto) {
        if (!is_array($produto)) {
            unset($_SESSION['carrinho'][$id]); // remove itens inválidos
        }
    }
}


$total = 0;
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Carrinho de Compras</h2>
        </div>
        <div class="card-body">
        <?php
        // Usuário logado → pega do banco
        if (SessionController::isLoggedIn()) {
            include "../../db/conexao.php";
            $userId = SessionController::getUserId();

            $stmt = $conexao->prepare("SELECT cr.id_carrinho, cr.qtd_carrinho, p.nome_produto, p.preco_produto
                                        FROM carrinho cr
                                        INNER JOIN produto p ON cr.id_produto = p.id_produto
                                        WHERE cr.id_user = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table class='table table-striped'>
                        <thead class='table-dark'>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                        </thead><tbody>";

                while ($row = $result->fetch_assoc()) {
                    $subtotal = $row['preco_produto'] * $row['qtd_carrinho'];
                    $total += $subtotal;
                    echo "<tr>
                            <td>{$row['nome_produto']}</td>
                            <td>R$ ".number_format($row['preco_produto'],2,',','.')."</td>
                            <td>{$row['qtd_carrinho']}</td>
                            <td>R$ ".number_format($subtotal,2,',','.')."</td>
                            <td>
                                <a class='btn btn-danger' href='../../controller/carrinho/delete.php?id_carrinho={$row['id_carrinho']}'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                    <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                                </a>
                            </td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-warning text-center'>Seu carrinho está vazio!</div>";
            }
            $conexao->close();

        } else {
            // Usuário NÃO logado → pega da sessão
            if (!empty($_SESSION['carrinho'])) {
                echo "<table class='table table-striped'>
                        <thead class='table-dark'>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                        </thead><tbody>";
                foreach ($_SESSION['carrinho'] as $id => $produto) {
                    $subtotal = $produto['preco'] * $produto['quantidade'];
                    $total += $subtotal;
                    echo "<tr>
                            <td>{$produto['nome']}</td>
                            <td>R$ ".number_format($produto['preco'],2,',','.')."</td>
                            <td>{$produto['quantidade']}</td>
                            <td>R$ ".number_format($subtotal,2,',','.')."</td>
                            <td>
                                <a class='btn btn-danger' href='../../controller/carrinho/delete_session.php?id={$id}'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                    <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/></a>
                            </td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-warning text-center p-4 fs-5'>
                        Seu carrinho está vazio!
                      </div>";
            }
        }

        if ($total > 0) {
            echo "<h4>Total: R$ ".number_format($total,2,',','.')."</h4>
                  <div class='d-flex justify-content-between mt-3'>
                    <a href='../telainicial/index.php' class='btn btn-primary'>Continuar Comprando</a>
                    <a href='../pedido/new.php' class='btn btn-success'>Finalizar Pedido</a>
                  </div>";
        }
        ?>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
