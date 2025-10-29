<?php 
// finalizar_itens.php
include '../session/Session.php';
session_start();
include "../../db/conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_pedido = $_GET['id_pedido'];

    // Pegar os itens do carrinho
    $userId = SessionController::getUserId();
    $sql = "SELECT cr.id_carrinho, cr.qtd_carrinho, p.id_produto FROM carrinho cr
            INNER JOIN produto p ON cr.id_produto = p.id_produto
            WHERE cr.id_user = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Inserir os itens do carrinho na tabela pedido_itens
    while ($row = $result->fetch_assoc()) {
        $id_produto = $row['id_produto'];
        $quantidade = $row['qtd_carrinho'];

        // Inserir item na tabela pedido_itens
        $stmt_item = $conexao->prepare("INSERT INTO pedido_item (id_pedido, id_produto, quantidade) VALUES (?, ?, ?)");
        $stmt_item->bind_param("iii", $id_pedido, $id_produto, $quantidade);
        $stmt_item->execute();
    }

    // Buscar dados do pedido para e-mail
    $orderQuery = "SELECT p.id_pedido, p.data_pedido, p.id_pagamento, p.id_bairro, p.complemento, p.mensagem, p.valor_total, p.id_logradouro, p.id_cidade
                   FROM pedido p WHERE p.id_pedido = ?";
    $orderStmt = $conexao->prepare($orderQuery);
    $orderStmt->bind_param("i", $id_pedido);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    $orderData = $orderResult->fetch_assoc();

    // Buscar itens do pedido para o e-mail
    $itemsQuery = "SELECT pi.quantidade, p.nome_produto, p.preco_produto
                   FROM pedido_item pi
                   INNER JOIN produto p ON pi.id_produto = p.id_produto
                   WHERE pi.id_pedido = ?";
    $itemsStmt = $conexao->prepare($itemsQuery);
    $itemsStmt->bind_param("i", $id_pedido);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();

    // Buscar informações do usuário
    $userQuery = "SELECT nome_user, email_user FROM usuarios WHERE id_user = ?";
    $userStmt = $conexao->prepare($userQuery);
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userData = $userResult->fetch_assoc();


    // Montar o conteúdo do e-mail em HTML
    $emailContent = "<h2>Detalhes do Pedido</h2>";
    $emailContent .= "<p><strong>Pedido ID:</strong> {$orderData['id_pedido']}</p>";
    $emailContent .= "<p><strong>Forma de Pagamento:</strong> " . ucwords($orderData['forma_pagamento']) . "</p>";

    
    $data_pedido = $orderData['data_pedido']; // Data original
    $data_formatada = date("d/m/Y H:i:s", strtotime($data_pedido)); // Formata a data
    $emailContent .= "<p><strong>Data:</strong> {$data_formatada}</p>";
    
    // Converte a primeira letra de cada palavra do endereço para maiúscula
    //$enderecoFormatado = ucwords(strtolower($orderData['endereco_entrega']));
    //$emailContent .= "<p><strong>Endereço de Entrega:</strong> {$enderecoFormatado}</p>";
    $emailContent .= "<p><strong>CEP:</strong> " . htmlspecialchars($orderData['cep']) . "</p>";
    $emailContent .= "<p><strong>Rua:</strong> " . ucwords(htmlspecialchars($orderData['rua'])) . "</p>";
    $emailContent .= "<p><strong>Bairro:</strong> " . ucwords(htmlspecialchars($orderData['bairro'])) . "</p>";
    if (!empty($orderData['complemento'])) {    
        $emailContent .= "<p><strong>Complemento:</strong> " . ucwords(htmlspecialchars($orderData['complemento'])) . "</p>";
    }
    
    
    $emailContent .= "<p><strong>Total:</strong> R$ " . number_format($orderData['total_pedido'], 2, ',', '.') . "</p>";
    $emailContent .= "<h3>Itens do Pedido</h3>";
    $emailContent .= "<table border='1' cellpadding='10'>
                        <thead>
                            <tr>
                                <th>Cesta</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>";   
    
    while ($item = $itemsResult->fetch_assoc()) {
        $subtotal = $item['preco_cesta'] * $item['qtd_itens'];
        
        // Converte a primeira letra de cada palavra do nome da cesta para maiúscula
        $nomeCestaFormatado = ucwords(strtolower($item['nome_cesta']));
        $emailContent .= "<tr>
                            <td>{$nomeCestaFormatado}</td>
                            <td>{$item['qtd_itens']}</td>
                            <td>R$ " . number_format($item['preco_cesta'], 2, ',', '.') . "</td>
                            <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
                        </tr>";
    }
    
    $emailContent .= "</tbody></table>";

    // Incluir informações do usuário no e-mail
    $emailContent .= "<h3>Informações do Cliente</h3>";
    $emailContent .= "<p><strong>Nome:</strong> " . ucwords(strtolower($userData['nome_user'])) . "</p>";
    $emailContent .= "<p><strong>E-mail:</strong> {$userData['email_user']}</p>";
    if (!empty($orderData['mensagem'])) {    
        $emailContent .= "<p><strong>Mensagem Especial:</strong> " . ucwords(htmlspecialchars($orderData['mensagem'])) . "</p>";
    }

    include '../MailController.php'; // Certifique-se de que o caminho está correto
    MailController::sendMail('caua.porciuncula@gmail.com', 'Novo Pedido Recebido', $emailContent);

    // Limpar o carrinho após finalizar o pedido
    $stmt_delete = $conexao->prepare("DELETE FROM carrinho WHERE id_user = ?");
    $stmt_delete->bind_param("i", $userId);
    $stmt_delete->execute();

    // Redirecionar para a página de confirmação
    header("Location: ../../views/carrinho/confirmacao_pedido.php?id_pedido=" . $id_pedido);
    exit();
}
?>
