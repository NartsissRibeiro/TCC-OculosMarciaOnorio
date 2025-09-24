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
                <a class='btn btn-danger' href='../../controller/carrinho/delete.php?id_carrinho=" . $row['id_carrinho'] . "'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
  <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
</svg></a>
                </td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<h4>Total: R$ " . number_format($total, 2, ',', '.') . "</h4>";
        echo "<div class='d-flex justify-content-end mt-3'><a href='../telainicial/index.php' class='btn'>Continuar Comprando</a></div>";
        echo "<div class='d-flex justify-content-end mt-3'><a href='../pedido/new.php' class='btn'>Finalizar pedido</a></div>";
    } else {
        echo "<div class='alert alert-warning mt-3'>Seu carrinho está vazio.</div>";
    }
    mysqli_close($conexao);

    } else {
    echo "<div class='alert alert-danger mt-3'>
            Você precisa estar logado para acessar o carrinho.
          </div>";
}