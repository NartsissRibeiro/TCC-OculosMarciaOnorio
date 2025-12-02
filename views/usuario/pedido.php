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
                    SELECT p.id_pedido, p.data_pedido, p.valor_total, st.tipo_status,
                           l.logradouro, b.nome_bairro, c.nome_cidade, e.nome_estado, p.complemento, p.mensagem
                    FROM pedido p
                    INNER JOIN status st ON p.id_status = st.id_status
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
                                    <th>Status</th>
                                    <th>Endereço</th>
                                    <th>Complemento</th>
                                    <th>Mensagem</th>
                                    <th>Produtos</th>
                                    <th>Quantidades</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>";

                    while ($pedido = $result->fetch_assoc()) {
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
                        echo "<td>" . htmlspecialchars(ucwords($pedido['tipo_status'])) . "</td>";
                        echo "<td>" . $endereco . "</td>";
                        echo "<td>" . htmlspecialchars($pedido['complemento'] ?? '-') . "</td>";
                        echo "<td>" . htmlspecialchars($pedido['mensagem'] ?? '-') . "</td>";
                        echo "<td class='text-start'>" . $produtosHtml . "</td>";
                        echo "<td class='text-start'>" . $quantidadesHtml . "</td>";

                        echo "<td>";
                        if (strtolower($pedido['tipo_status']) == 'não pago') {
                            echo "<a href='../pagamento/new.php?id_pedido=" . $pedido['id_pedido'] . "' class='btn btn-success btn-sm me-1'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-bag-fill' viewBox='0 0 16 16'>
                                <path d='M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z'/>
                                </svg>
                                  </a>";
                            
                            echo "<a href='editar_endereco.php?idpedido=" . $pedido['id_pedido'] . "' class='btn btn-warning btn-sm'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
                                          <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z'/>
                                        </svg>
                              </a>";
                            
                            echo "<a href='../../controller/pedido/delete.php?idpedido=" . $pedido['id_pedido'] . "'
                                class='btn btn-danger btn-sm'
                                onclick=\"return confirm('Tem certeza que deseja excluir este pedido?');\">
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                          <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                                        </svg>
                            </a>";
                        }

                        echo "</td>";

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
