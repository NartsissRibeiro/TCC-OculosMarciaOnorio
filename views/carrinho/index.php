<?php include "../partials/header.php"; ?>
<?php include "../partials/navbar.php"; ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Carrinho de Compras</h2>
        </div>
        <div class="card-body">
        <?php
        include "../../db/conexao.php";
           if (SessionController::isLoggedIn()) {
    $userId = SessionController::getUserId();

    $stmt = $conexao->prepare("SELECT 
                                cr.id_carrinho, 
                                cr.qtd_carrinho,
                                p.imagem_produto, 
                                p.nome_produto, 
                                p.preco_produto, 
                                m.nome_material, 
                                c.nome_cor
                            FROM carrinho cr
                            INNER JOIN produto p ON cr.id_produto = p.id_produto
                            LEFT JOIN material m ON p.id_material = m.id_material
                            LEFT JOIN cor c ON p.id_cor = c.id_cor
                            WHERE cr.id_user = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0) {
        $total = 0;
        echo "<table class='table table-striped'>
                <thead class='table-dark'>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Material</th>
                        <th>Cor</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            $total += $row['preco_produto'] * $row['qtd_carrinho'];

            echo "<tr>";
            //echo "<td>" . htmlspecialchars($row['imagem_produto']) . "</td>";
            echo "<td>" . ucwords(htmlspecialchars($row['nome_produto'])) . "</td>";
            echo "<td>R$ " . number_format($row['preco_produto'], 2, ',', '.') . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_material'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_cor'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['qtd_carrinho']) . "</td>";
            echo "<td>
                <a class='btn btn-danger' href='../../controller/carrinho/delete.php?id_carrinho=" . $row['id_carrinho'] . "'>Deletar</a>
                </td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<h4>Total: R$ " . number_format($total, 2, ',', '.') . "</h4>";
        echo "<div class='d-flex justify-content-end mt-3'><a href='../telainicial/index.php' class='btn'>Continuar Comprando</a></div>";
        echo "<div class='d-flex justify-content-end mt-3'><a href='./pedido.php' class='btn'>Fazer Pedido</a></div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>
                Você ainda não adicionou nenhum produto ao carrinho.
              </div>";
    }
} else {
    echo "<div class='alert alert-warning' role='alert'>
            Usuário não está logado. Por favor, faça login para acessar seu carrinho de compras.
          </div>";
}
        ?>
        </div>
    </div>
</div>