<?php
session_start();
include_once "../../Controller/Session/Session.php";
include "../../db/conexao.php";
require_once "../../Controller/MailController.php";

if (!SessionController::isLoggedIn()) {
    die("<div class='alert alert-danger'>Você precisa estar logado para finalizar o pedido.</div>");
}

$userId = SessionController::getUserId();

$total = $_POST['total'] ?? 0;
$produtos = $_POST['produtos'] ?? [];
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$bairroNome = $_POST['bairro'] ?? '';
$cidadeNome = $_POST['cidade'] ?? '';
$estadoNome = $_POST['estado'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? null;
$mensagem = $_POST['mensagem'] ?? null;

$conexao->begin_transaction();

try {
    $stmt = $conexao->prepare("SELECT id_estado FROM estado WHERE nome_estado = ?");
    $stmt->bind_param("s", $estadoNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $estadoId = $res->fetch_assoc()['id_estado'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO estado (nome_estado) VALUES (?)");
        $stmt->bind_param("s", $estadoNome);
        $stmt->execute();
        $estadoId = $conexao->insert_id;
    }

    $stmt = $conexao->prepare("SELECT id_cidade FROM cidade WHERE nome_cidade = ?");
    $stmt->bind_param("s", $cidadeNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $cidadeId = $res->fetch_assoc()['id_cidade'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO cidade (nome_cidade, id_estado) VALUES (?, ?)");
        $stmt->bind_param("si", $cidadeNome, $estadoId);
        $stmt->execute();
        $cidadeId = $conexao->insert_id;
    }

    $stmt = $conexao->prepare("SELECT id_bairro FROM bairro WHERE nome_bairro = ?");
    $stmt->bind_param("s", $bairroNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $bairroId = $res->fetch_assoc()['id_bairro'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO bairro (nome_bairro, cep) VALUES (?, ?)");
        $stmt->bind_param("ss", $bairroNome, $cep);
        $stmt->execute();
        $bairroId = $conexao->insert_id;
    }

    $logradouroCompleto = $rua . ', ' . $numero;
    $stmt = $conexao->prepare("INSERT INTO logradouro (logradouro, id_tipo) VALUES (?, 1)");
    $stmt->bind_param("s", $logradouroCompleto);
    $stmt->execute();
    $logradouroId = $conexao->insert_id;

    $acao = $_POST['acao'] ?? 'pago';
    $statusId = ($acao === 'nao_pago') ? 2 : 1; 

    $stmt = $conexao->prepare("
        INSERT INTO pedido 
        (data_pedido, id_status, id_bairro, complemento, mensagem, valor_total, id_logradouro, id_user, id_cidade)
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iissdiii",
        $statusId,
        $bairroId,
        $complemento,
        $mensagem,
        $total,
        $logradouroId,
        $userId,
        $cidadeId
    );
    $stmt->execute();
    $pedidoId = $conexao->insert_id;

    $body = "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
    <meta charset='UTF-8'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            background-color: #fff;
            margin: 30px auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #ffb2cbff;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .email-header h2 {
            margin: 0;
            font-size: 22px;
        }
        .email-body {
            padding: 25px;
        }
        .email-body p {
            margin: 10px 0;
            font-size: 15px;
        }
        .product-list {
            margin: 20px 0;
            border-collapse: collapse;
            width: 100%;
        }
        .product-list th, .product-list td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .product-list th {
            background-color: #f0f0f0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #ffb2cbff;
            margin-top: 15px;
        }
        .footer {
            background-color: #f0f0f0;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #666;
        }
    </style>
    </head>
    <body>

    <div class='email-container'>
        <div class='email-header'>
            <h2>Pedido Confirmado #$pedidoId</h2>
        </div>

        <div class='email-body'>
            <p>Olá! Seu pedido foi registrado com sucesso.</p>
            <p>Abaixo estão os detalhes da sua compra:</p>

            <table class='product-list'>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>";
                
    foreach ($produtos as $id_produto => $quantidade) {
        $stmtProd = $conexao->prepare("SELECT nome_produto, preco_produto FROM produto WHERE id_produto = ?");
        $stmtProd->bind_param("i", $id_produto);
        $stmtProd->execute();
        $resProd = $stmtProd->get_result()->fetch_assoc();
        $subtotal = $resProd['preco_produto'] * $quantidade;

        $body .= "
            <tr>
                <td>{$resProd['nome_produto']}</td>
                <td style='text-align:center;'>$quantidade</td>
                <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
            </tr>";
    }

    $body .= "
                </tbody>
            </table>

            <p class='total'>Total: R$ " . number_format($total, 2, ',', '.') . "</p>

            <p><strong>Endereço de entrega:</strong><br>
            $rua, $numero, $bairroNome, $cidadeNome - $estadoNome</p>";

    if ($mensagem) {
        $body .= "<p><strong>Mensagem:</strong> $mensagem</p>";
    }

    $body .= "
            <p>Você receberá atualizações sobre seu pedido por e-mail.</p>
        </div>

        <div class='footer'>
            <p>Obrigado por comprar conosco!</p>
            <p>Equipe <strong>OculosMarciaOnorio.com</strong></p>
        </div>
    </div>

    </body>
    </html>
    ";

    $stmtUser = $conexao->prepare("SELECT email_user FROM usuarios WHERE id_user = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $userEmail = $stmtUser->get_result()->fetch_assoc()['email_user'];

    MailController::sendMail($userEmail, "Confirmação do Pedido #$pedidoId", $body);

    $stmtItem = $conexao->prepare("INSERT INTO pedido_item (id_pedido, id_produto, quantidade) VALUES (?, ?, ?)");
    foreach ($produtos as $idProduto => $qtd) {
        $stmtItem->bind_param("iii", $pedidoId, $idProduto, $qtd);
        $stmtItem->execute();
    }

    $stmt = $conexao->prepare("DELETE FROM carrinho WHERE id_user = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $conexao->commit();

    header("Location: ../../views/pedido/sucesso.php?id_pedido=$pedidoId");
    exit;

} catch (Exception $e) {
    $conexao->rollback();
    echo "<div class='alert alert-danger'>Erro ao finalizar pedido: " . $e->getMessage() . "</div>";
} 
?>
