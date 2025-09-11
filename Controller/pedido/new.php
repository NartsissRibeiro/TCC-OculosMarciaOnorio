<?php
include '../Session/Session.php';
session_start();
date_default_timezone_set('America/Sao_Paulo');
include "../../db/conexao.php"; 


if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    $data_pedido = date('Y-m-d H:i:s');
    $id_pagamento = $_POST['id_pagamento']; 
    $id_bairro = $_POST['id_bairro'];
    $logradouro = $_POST['id_tipo'];
    $id_cidade = $_POST['id_cidade'];
    $complemento = isset($_POST['complemento']) ? $_POST['complemento'] : NULL; 
    $mensagem = $_POST['mensagem'];
    $valor_total = $_POST['valor_total'];


    // Inserir um novo pedido na tabela 'pedido'
    $query = "INSERT INTO pedido (id_user, data_pedido, total_pedido, forma_pagamento, cep, numero, bairro, complemento, rua, mensagem) 
              VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Verifique os tipos de dados, ajustando conforme necessário para o banco de dados.
    $stmt = $conn->prepare($query);
    $stmt->bind_param("idsiissss", $id_user, $total, $pagamento, $cep, $numero, $bairro, $complemento, $rua, $mensagem);
    
    // Execute a query
    if ($stmt->execute()) {
        // Pegar o ID do pedido recém-criado
        $id_pedido = $stmt->insert_id;

        // Redirecionar para a próxima etapa (finalização dos itens)
        header("Location: finalizar_itens.php?id_pedido=" . $id_pedido);
        exit(); // Não se esqueça do exit() para garantir que o código pare por aqui.
    } else {
        echo "Erro ao inserir pedido: " . $stmt->error;
        exit(); // Caso o INSERT falhe, pare a execução.
    }
} else {
    echo "Erro: Usuário não logado ou endereço de entrega inválido.";
    exit(); // Para garantir que o código pare aqui em caso de erro.
}
?> 

<?php
date_default_timezone_set('America/Sao_Paulo');
include "../partials/header.php";
include "../partials/navbar.php"; 
include "../../db/conexao.php"; 


if (isset($_GET['id_pedido'])) {
    $id_pedido = intval($_GET['id_pedido']);

    $sql = "SELECT p.id_pedido, p.data_pedido, p.valor_total, b.cep, tl.tipo_logradouro, p.bairro, p.numero, c.nome_cesta, pi.quantidade, c.preco_cesta
            FROM pedido p
            LEFT JOIN bairro b ON p.id_pedido = b.id_pedido
            LEFT JOIN tipo_logradouro tl ON p.id_pedido = tl.id_pedido 
            LEFT JOIN cidade cd ON p.id_pedido = cd.id_pedido
            JOIN pedido_item pi ON p.id_pedido = pi.id_pedido
            JOIN produto pr ON pi.id_produto = pr.id_produto
            WHERE p.id_pedido = $id_pedido";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<div class='container mt-5'>";
        echo "<h2 class='text-center'>Confirmação do Pedido</h2>";
        echo "<div class='card mb-4'>
                <div class='card-body'>
                    <p>ID do Pedido: <strong>" . htmlspecialchars($id_pedido) . "</strong></p>";

        // Inicializa variável para armazenar total e endereço
        $total_pedido = 0;
        $endereco_entrega = '';

        // Detalhes do pedido
        while ($row = mysqli_fetch_assoc($result)) {
            // Acumular o total do pedido
            $total_pedido = $row['total_pedido'];
            $forma_pagamento = $row['forma_pagamento']; 
            $cep = $row['cep'];
            $rua = $row['rua'];
            $numero = $row['numero'];
            $bairro = $row['bairro'];
            if ($row['complemento']) {
                $complemento = $row['complemento'];
            }


        }

        // Exibir o endereço de entrega
        echo "<h4>Endereço de Entrega:</h4>";
        echo "<p><strong>CEP:</strong> " . ucwords(htmlspecialchars($cep)) . "</p>";
        echo "<p><strong>Rua:</strong> " . ucwords(htmlspecialchars($rua)) . "</p>";
        echo "<p><strong>Número:</strong> " . htmlspecialchars($numero) . "</p>";
        echo "<p><strong>Bairro:</strong> " . ucwords(htmlspecialchars($bairro)) . "</p>";
        if (isset($complemento)) {
            echo "<p><strong>Complemento:</strong> " . ucwords(htmlspecialchars($complemento)) . "</p>";
        }
        
        
        echo "<h4>Cestas Pedidas:</h4>";
        echo "<table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>Cesta</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>";
        
        // Reiniciar a consulta para percorrer novamente os resultados
        mysqli_data_seek($result, 0); // Volta o ponteiro do resultado para o início
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars(ucwords(strtolower($row['nome_cesta']))) . "</td> <!-- Nome da cesta com primeira letra maiúscula -->
                    <td>R$ " . number_format($row['preco_cesta'], 2, ',', '.') . "</td>
                    <td>" . htmlspecialchars($row['qtd_itens']) . "</td>
                  </tr>";
        }
        echo "</tbody></table>";

        echo "<h5>Data do Pedido: " . date("d/m/Y H:i:s") . "</h5>"; // Exibir a data atual como data do pedido
        echo "<h5><strong>Forma de Pagamento: </strong> ". ucwords($forma_pagamento)."</h5>";
        
        // Exibir o total fora do loop
        echo "<div class='d-flex justify-content-end align-items-center mt-4'>
                <h4 class='me-2'><strong>Total:</strong></h4>
                <h4>R$ " . number_format($total_pedido, 2, ',', '.') . "</h4>
              </div>";
        
        // Botão para voltar à tela inicial
        echo "<div class='text-center mt-4'>
                <a href='../telainicial' class='btn btn-primary'>Voltar à Tela Inicial</a>
              </div>";

        echo "</div></div>"; // Fecha a card
    } else {
        echo "<div class='alert alert-danger' role='alert'>Pedido não encontrado.</div>";
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>ID do pedido não especificado.</div>";
}

mysqli_close($conexao);
include "../partials/footer.php"; 
?>
