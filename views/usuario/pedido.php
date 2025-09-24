<?php 
session_start();
include "../partials/header.php";
include "../partials/navbar.php";
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Meus Pedidos</h2>
        </div>
        <div class="card-body">
            <?php
            if (SessionController::isLoggedIn()) {
                $userId = SessionController::getUserId();

                $stmt = $conexao->prepare("
                    SELECT p.id_pedido, p.data_pedido, p.valor_total, pg.tipo_pagamento,
                           l.logradouro, b.nome_bairro, c.nome_cidade, e.nome_estado, p.complemento, p.mensagem
                    FROM pedido p
                    INNER JOIN pagamento pg ON p.id_pagamento = pg.id_pagamento
                    INNER JOIN logradouro l ON p.id_logradouro = l.id_logradouro
                    INNER JOIN bairro b ON p.id_bairro = b.id_bairro
                    INNER JOIN cidade c ON p.id_cidade = c.id_cidade
                    INNER JOIN estado e ON c.id_estado = e.id_estado
                    WHERE p.id_user = ?
                    ORDER BY p.data_pedido DESC
                ");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped table-bordered'>
                            <thead class='table-dark text-center'>
                                <tr>
                                    <th>Pedido ID</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Pagamento</th>
                                    <th>Endereço</th>
                                    <th>Mensagem</th>
                                    <th>Produtos</th>
                                    <th>Quantidades</th>
                                </tr>
                            </thead>
                            <tbody>";
                    
                    while ($pedido = $result->fetch_assoc()) {
                        // Busca produtos do pedido
                        $stmtItems = $conexao->prepare("
                            SELECT pr.nome_produto, pi.quantidade
                            FROM pedido_item pi
                            INNER JOIN produto pr ON pi.id_produto = pr.id_produto
                            WHERE pi.id_pedido = ?
                        ");
                        $stmtItems->bind_param("i", $pedido['id_pedido']);
                        $stmtItems->execute();
                        $itensResult = $stmtItems->get_result();

                        $produtosHtml = "<ul class='mb-0'>";
                        $quantidadesHtml = "<ul class='mb-0'>";
                        while ($item = $itensResult->fetch_assoc()) {
                            $produtosHtml .= "<li>" . htmlspecialchars($item['nome_produto']) . "</li>";
                            $quantidadesHtml .= "<li>" . $item['quantidade'] . "</li>";
                        }
                        $produtosHtml .= "</ul>";
                        $quantidadesHtml .= "</ul>";

                        $endereco = htmlspecialchars($pedido['logradouro']) . ", " . 
                                    htmlspecialchars($pedido['nome_bairro']) . " - " . 
                                    htmlspecialchars($pedido['nome_cidade']) . "/" . 
                                    htmlspecialchars($pedido['nome_estado']);

                        echo "<tr class='align-middle text-center'>";
                        echo "<td>" . $pedido['id_pedido'] . "</td>";
                        echo "<td>" . date('d/m/Y H:i:s', strtotime($pedido['data_pedido'])) . "</td>";
                        echo "<td>R$ " . number_format($pedido['valor_total'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars(ucwords($pedido['tipo_pagamento'])) . "</td>";
                        echo "<td>" . $endereco . "</td>";
                        echo "<td>" . htmlspecialchars($pedido['mensagem'] ?? '-') . "</td>";
                        echo "<td class='text-start'>" . $produtosHtml . "</td>";
                        echo "<td class='text-start'>" . $quantidadesHtml . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<div class='alert alert-info text-center' role='alert'>
                            Você ainda não fez nenhum pedido.
                          </div>";
                }

            } else {
                echo "<div class='alert alert-warning text-center' role='alert'>
                        Usuário não está logado. Por favor, faça login para acessar seus pedidos.
                      </div>";
            }

            mysqli_close($conexao);
            ?>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>
